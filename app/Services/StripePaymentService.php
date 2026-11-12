<?php

namespace App\Services;

use App\Models\EscrowAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentRefund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePaymentService
{
    private ?StripeClient $stripe = null;
    private bool $isFake = false;

    public function __construct()
    {
        $secret = config('stripe.secret');

        $this->isFake = empty($secret) || str_contains($secret, 'placeholder');
        if ($this->isFake) {
            Log::warning('Stripe running in FAKE mode — STRIPE_SECRET is missing or placeholder. Card payments will be simulated without calling Stripe API. Set real keys in .env for production.');
            return;
        }

        $this->stripe = new StripeClient($secret);
    }

    public function isFake(): bool
    {
        return $this->isFake;
    }

    /**
     * Create or reuse a Stripe PaymentIntent for the order.
     * Uses idempotency_key to prevent duplicate charges on retry.
     */
    public function createPaymentIntent(Order $order): array
    {
        // ── FAKE MODE: generate local mock intent without calling Stripe API ──
        if ($this->isFake) {
            $existingPayment = $order->payment;
            if ($existingPayment?->payment_gateway === 'stripe' && str_starts_with($existingPayment->transaction_id ?? '', 'pi_fake_')) {
                // Reuse existing fake intent if still pending
                if (in_array($existingPayment->status, ['pending'], true)) {
                    $fakeSecret = ($existingPayment->gateway_response['fake_client_secret'] ?? $existingPayment->transaction_id . '_secret_fake');
                    return [
                        'payment' => $existingPayment,
                        'client_secret' => $fakeSecret,
                        'is_fake' => true,
                    ];
                }
            }
            $currency = config('stripe.currency', 'pkr');
            $fakeId = 'pi_fake_' . substr(md5(uniqid((string) $order->id, true)), 0, 24);
            $fakeSecret = $fakeId . '_secret_fake_' . substr(md5(uniqid()), 0, 16);
            return DB::transaction(function () use ($order, $fakeId, $fakeSecret, $currency, $existingPayment) {
                if ($existingPayment && $existingPayment->payment_gateway === 'stripe') {
                    $existingPayment->update([
                        'amount' => $order->total_amount,
                        'currency' => strtoupper($currency),
                        'transaction_id' => $fakeId,
                        'status' => 'pending',
                        'escrow_status' => 'held',
                        'gateway_response' => array_merge($existingPayment->gateway_response ?? [], [
                            'provider' => 'stripe_fake',
                            'payment_intent_id' => $fakeId,
                            'fake_client_secret' => $fakeSecret,
                            'updated_at' => now()->toIso8601String(),
                            'note' => 'FAKE mode — no real Stripe call. Set real STRIPE_SECRET for production.',
                        ]),
                    ]);
                    $payment = $existingPayment;
                } else {
                    $payment = Payment::create([
                        'order_id' => $order->id,
                        'amount' => $order->total_amount,
                        'currency' => strtoupper($currency),
                        'payment_method' => 'card',
                        'payment_gateway' => 'stripe',
                        'transaction_id' => $fakeId,
                        'status' => 'pending',
                        'escrow_status' => 'held',
                        'gateway_response' => [
                            'provider' => 'stripe_fake',
                            'payment_intent_id' => $fakeId,
                            'fake_client_secret' => $fakeSecret,
                            'created_at' => now()->toIso8601String(),
                            'note' => 'FAKE mode — no real Stripe call. Set real STRIPE_SECRET for production.',
                        ],
                    ]);
                }
                $order->update([
                    'payment_id' => $payment->id,
                    'payment_status' => 'pending',
                ]);
                EscrowAccount::firstOrCreate(
                    ['payment_id' => $payment->id],
                    ['amount' => $payment->amount, 'status' => 'held', 'held_at' => now(), 'description' => 'Stripe FAKE escrow hold for order ' . $order->order_number]
                );
                return [
                    'payment' => $payment,
                    'client_secret' => $fakeSecret,
                    'is_fake' => true,
                ];
            });
        }
        // Reuse existing pending intent to avoid duplicates
        $existingPayment = $order->payment;
        if ($existingPayment?->payment_gateway === 'stripe' && str_starts_with($existingPayment->transaction_id ?? '', 'pi_')) {
            try {
                $intent = $this->stripe->paymentIntents->retrieve($existingPayment->transaction_id, []);
                // If intent is still usable, return it
                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action', 'processing'], true)) {
                    return [
                        'payment' => $existingPayment,
                        'client_secret' => $intent->client_secret,
                    ];
                }
                // If succeeded already, mark completed and create escrow if missing
                if ($intent->status === 'succeeded' && $existingPayment->status !== 'completed') {
                    $this->markCompleted($existingPayment, $intent);
                    return [
                        'payment' => $existingPayment->fresh(),
                        'client_secret' => $intent->client_secret,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('Stripe retrieve intent failed, creating new one', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        $currency = config('stripe.currency', 'pkr');
        $amount = (int) round(((float) $order->total_amount) * 100);

        $minimums = [
            'pkr' => 15000, // ~Rs 150 — ~$0.50 USD minimum (Stripe: amount must convert to >= 50 cents)
            'usd' => 50,
            'eur' => 50,
            'gbp' => 30,
            'inr' => 5000, // Rs 50
        ];
        $min = $minimums[$currency] ?? 50;
        if ($amount < $min) {
            $minDisplay = number_format($min / 100, 2);
            $currencyLabel = strtoupper($currency);
            throw new \RuntimeException("Order amount too small for card payment. Minimum is {$minDisplay} {$currencyLabel}. Add more items or use Cash on Delivery.");
        }

        $intent = $this->stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
            'description' => config('stripe.description') . ' #' . $order->order_number,
            'metadata' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'customer_id' => (string) $order->customer_id,
            ],
        ], [
            'idempotency_key' => 'vandorax-order-' . $order->id . '-' . substr(md5($order->order_number), 0, 8),
        ]);

        return DB::transaction(function () use ($order, $intent, $currency, $existingPayment) {
            if ($existingPayment && $existingPayment->payment_gateway === 'stripe') {
                $existingPayment->update([
                    'amount' => $order->total_amount,
                    'currency' => strtoupper($currency),
                    'transaction_id' => $intent->id,
                    'status' => 'pending',
                    'escrow_status' => 'held',
                    'gateway_response' => array_merge($existingPayment->gateway_response ?? [], [
                        'provider' => 'stripe',
                        'payment_intent_id' => $intent->id,
                        'updated_at' => now()->toIso8601String(),
                    ]),
                ]);
                $payment = $existingPayment;
            } else {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'currency' => strtoupper($currency),
                    'payment_method' => 'card',
                    'payment_gateway' => 'stripe',
                    'transaction_id' => $intent->id,
                    'status' => 'pending',
                    'escrow_status' => 'held',
                    'gateway_response' => [
                        'provider' => 'stripe',
                        'payment_intent_id' => $intent->id,
                        'created_at' => now()->toIso8601String(),
                    ],
                ]);
            }

            $order->update([
                'payment_id' => $payment->id,
                'payment_status' => 'pending',
            ]);

            // Ensure escrow record exists (funds held)
            EscrowAccount::firstOrCreate(
                ['payment_id' => $payment->id],
                ['amount' => $payment->amount, 'status' => 'held', 'held_at' => now(), 'description' => 'Stripe escrow hold for order ' . $order->order_number]
            );

            return [
                'payment' => $payment,
                'client_secret' => $intent->client_secret,
            ];
        });
    }

    /**
     * Retrieve current status of a payment intent (for polling / retry UI).
     */
    public function getPaymentStatus(Payment $payment): array
    {
        if ($this->isFake) {
            return ['status' => $payment->status, 'escrow' => $payment->escrow_status, 'gateway_status' => $payment->status === 'completed' ? 'succeeded' : 'requires_payment_method', 'is_fake' => true];
        }
        if (!str_starts_with($payment->transaction_id ?? '', 'pi_')) {
            return ['status' => $payment->status, 'escrow' => $payment->escrow_status, 'gateway_status' => null];
        }
        try {
            $intent = $this->stripe->paymentIntents->retrieve($payment->transaction_id, []);
            return [
                'status' => $payment->status,
                'escrow' => $payment->escrow_status,
                'gateway_status' => $intent->status,
                'amount' => $intent->amount / 100,
                'currency' => strtoupper($intent->currency),
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe status check failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return ['status' => $payment->status, 'escrow' => $payment->escrow_status, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel and allow retry — creates a new intent on next createPaymentIntent call.
     */
    public function retryPayment(Order $order): array
    {
        if ($this->isFake) {
            return $this->createPaymentIntent($order->fresh());
        }
        $payment = $order->payment;
        if ($payment?->payment_gateway === 'stripe' && str_starts_with($payment->transaction_id ?? '', 'pi_')) {
            try {
                $intent = $this->stripe->paymentIntents->retrieve($payment->transaction_id, []);
                if (in_array($intent->status, ['requires_payment_method', 'requires_action'], true)) {
                    // Reuse same intent
                    return ['payment' => $payment, 'client_secret' => $intent->client_secret];
                }
                if ($intent->status === 'canceled' || $intent->status === 'requires_payment_method') {
                    // Will create new below
                } else {
                    // Cancel stale intent if possible
                    try { $this->stripe->paymentIntents->cancel($payment->transaction_id, []); } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {}
        }
        return $this->createPaymentIntent($order->fresh());
    }

    /**
     * Refund via Stripe (full or partial). Updates DB and escrow.
     */
    public function refundPayment(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        if ($payment->payment_gateway !== 'stripe' || !str_starts_with($payment->transaction_id ?? '', 'pi_')) {
            throw new \RuntimeException('Only Stripe card payments can be refunded via Stripe.');
        }
        if (!in_array($payment->status, ['completed'], true)) {
            throw new \RuntimeException('Only completed payments can be refunded.');
        }

        if ($this->isFake) {
            // Fake refund — no Stripe API call
            DB::transaction(function () use ($payment, $reason) {
                $payment->update([
                    'status' => 'refunded',
                    'escrow_status' => 'refunded',
                    'refunded_at' => now(),
                    'refund_reason' => $reason ?: 'Refunded (FAKE mode)',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], [
                        'refund_id' => 're_fake_' . substr(md5(uniqid()), 0, 16),
                        'refund_status' => 'succeeded',
                        'fake' => true,
                    ]),
                ]);
                $payment->order->update(['payment_status' => 'refunded']);
                $escrow = EscrowAccount::where('payment_id', $payment->id)->first();
                $escrow?->update(['status' => 'refunded', 'refunded_at' => now(), 'description' => $reason ?: 'Refunded (fake)']);
            });
            return ['refund' => (object) ['id' => 're_fake', 'status' => 'succeeded'], 'payment' => $payment->fresh()];
        }

        $params = ['payment_intent' => $payment->transaction_id];
        if ($amount !== null) {
            $params['amount'] = (int) round($amount * 100);
        }
        if ($reason) {
            $params['metadata'] = ['reason' => $reason];
        }

        $refund = $this->stripe->refunds->create($params);

        DB::transaction(function () use ($payment, $refund, $reason) {
            $payment->update([
                'status' => 'refunded',
                'escrow_status' => 'refunded',
                'refunded_at' => now(),
                'refund_reason' => $reason ?: 'Refunded via Stripe',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'refund_id' => $refund->id,
                    'refund_status' => $refund->status,
                ]),
            ]);
            $payment->order->update(['payment_status' => 'refunded']);
            $escrow = EscrowAccount::where('payment_id', $payment->id)->first();
            $escrow?->update(['status' => 'refunded', 'refunded_at' => now(), 'description' => $reason ?: 'Refunded']);
        });

        return ['refund' => $refund, 'payment' => $payment->fresh()];
    }

    /**
     * Refund one returned item without marking the entire payment refunded.
     */
    public function refundAmount(Payment $payment, float $amount, string $reason = ''): object
    {
        if ($payment->payment_gateway !== 'stripe' || !str_starts_with($payment->transaction_id ?? '', 'pi_')) {
            throw new \RuntimeException('This payment gateway cannot issue an original-card refund automatically.');
        }
        if ($payment->status !== 'completed') {
            throw new \RuntimeException('Only completed payments can be refunded.');
        }
        if ($amount <= 0 || $amount > (float) $payment->amount - (float) $payment->refunded_amount) {
            throw new \RuntimeException('The refund amount exceeds the remaining payment balance.');
        }

        if ($this->isFake) {
            $refund = (object) [
                'id' => 're_fake_' . substr(md5(uniqid()), 0, 16),
                'status' => 'succeeded',
            ];
        } else {
            $params = [
                'payment_intent' => $payment->transaction_id,
                'amount' => (int) round($amount * 100),
            ];
            if ($reason) {
                $params['metadata'] = ['reason' => $reason];
            }
            $refund = $this->stripe->refunds->create($params);
            if (($refund->status ?? null) !== 'succeeded' && ($refund->status ?? null) !== 'pending') {
                throw new \RuntimeException('The card provider did not accept the refund.');
            }
        }

        DB::transaction(function () use ($payment, $amount, $refund, $reason): void {
            $payment->refresh();
            $refundedTotal = round((float) $payment->refunded_amount + $amount, 2);
            $fullyRefunded = $refundedTotal >= (float) $payment->amount;

            PaymentRefund::create([
                'payment_id' => $payment->id,
                'transaction_id' => $refund->id,
                'amount' => $amount,
                'currency' => $payment->currency,
                'status' => $refund->status ?? 'succeeded',
                'reason' => $reason ?: 'Refunded by admin',
                'processed_by' => auth()->id(),
                'gateway_response' => [
                    'refund_id' => $refund->id,
                    'refund_status' => $refund->status ?? null,
                ],
            ]);

            $payment->update([
                'refunded_amount' => $refundedTotal,
                'refunded_at' => $fullyRefunded ? now() : $payment->refunded_at,
                'status' => $fullyRefunded ? 'refunded' : 'completed',
                'escrow_status' => $fullyRefunded ? 'refunded' : 'held',
                'refund_reason' => $reason ?: 'Refunded by admin',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'last_refund_id' => $refund->id,
                    'last_refund_amount' => $amount,
                ]),
            ]);

            $escrow = EscrowAccount::where('payment_id', $payment->id)->lockForUpdate()->first();
            if ($escrow) {
                $remaining = max(0, round((float) $escrow->amount - $refundedTotal, 2));
                $escrow->update([
                    'refunded_amount' => $refundedTotal,
                    'remaining_amount' => $remaining,
                    'status' => $fullyRefunded ? 'refunded' : 'held',
                    'refunded_at' => $fullyRefunded ? now() : $escrow->refunded_at,
                    'description' => $reason ?: 'Partial refund processed',
                ]);
            }

            $payment->order->update([
                'payment_status' => $fullyRefunded ? 'refunded' : 'completed',
            ]);
        });

        return $refund;
    }

    public function handleWebhookEvent(object $event): void
    {
        if ($this->isFake) return;
        $intent = $event->data->object ?? null;
        if (!$intent || empty($intent->id)) return;

        $payment = Payment::where('transaction_id', $intent->id)->with('order')->first();
        if (!$payment) return;

        if ($event->type === 'payment_intent.succeeded') {
            $this->markCompleted($payment, $intent, $event->type);
        } elseif (in_array($event->type, ['payment_intent.payment_failed', 'payment_intent.canceled'], true)) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'last_event' => $event->type,
                    'stripe_status' => $intent->status ?? null,
                    'failure_message' => $intent->last_payment_error->message ?? null,
                ]),
            ]);
            $payment->order->update(['payment_status' => 'failed']);
        } elseif ($event->type === 'charge.refunded' || $event->type === 'refund.created') {
            $payment->update([
                'status' => 'refunded',
                'escrow_status' => 'refunded',
                'refunded_at' => now(),
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['last_event' => $event->type]),
            ]);
            $payment->order->update(['payment_status' => 'refunded']);
        }
    }

    public function markCompleted(Payment $payment, $intent, string $eventType = 'payment_intent.succeeded'): void
    {
        $wasCompleted = $payment->status === 'completed';
        DB::transaction(function () use ($payment, $intent, $eventType, $wasCompleted) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => $payment->paid_at ?? now(),
                'escrow_status' => 'held',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'last_event' => $eventType,
                    'stripe_status' => $intent->status ?? 'succeeded',
                ]),
            ]);
            $payment->order->update(['payment_status' => 'completed']);
            EscrowAccount::firstOrCreate(
                ['payment_id' => $payment->id],
                ['amount' => $payment->amount, 'status' => 'held', 'held_at' => now(), 'description' => 'Stripe escrow hold']
            );
            // Ensure escrow is held
            EscrowAccount::where('payment_id', $payment->id)->where('status', '!=', 'held')->update(['status' => 'held']);
        });

        if (!$wasCompleted) {
            try {
                app(PaymentNotificationService::class)->sendPaymentReceived($payment->order);
            } catch (\Throwable $e) {
                Log::error('Payment notification failed after Stripe success', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            }
        }
    }
}
