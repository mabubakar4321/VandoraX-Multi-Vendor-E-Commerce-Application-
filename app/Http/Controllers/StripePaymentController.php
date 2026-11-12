<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\StripePaymentService;
use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class StripePaymentController extends Controller
{
    private function stripeLimiterKey(Request $request): string
    {
        return 'stripe-pay:' . ($request->user()?->id ?? $request->ip());
    }

    private function ensureRateLimit(Request $request)
    {
        $key = $this->stripeLimiterKey($request);
        $max = (int) config('stripe.rate_limit.attempts', 5);
        $decay = (int) config('stripe.rate_limit.decay_minutes', 1) * 60;
        if (RateLimiter::tooManyAttempts($key, $max)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => 'Too many payment attempts. Please wait ' . $seconds . ' seconds and try again.',
            ], 429);
        }
        RateLimiter::hit($key, $decay);
        return null;
    }

    public function createIntent(PaymentRequest $request, StripePaymentService $stripe)
    {
        $limited = $this->ensureRateLimit($request);
        if ($limited) return $limited;

        $order = Order::with('payment')->findOrFail($request->order_id);

        if ($order->customer_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. This order does not belong to you.'], 403);
        }

        if ($order->payment_status === 'completed') {
            return response()->json(['success' => false, 'message' => 'This order has already been paid.'], 422);
        }

        try {
            $result = $stripe->createPaymentIntent($order);
            return response()->json([
                'success' => true,
                'message' => 'Payment session created. Enter your card details to complete payment.',
                'client_secret' => $result['client_secret'],
                'publishable_key' => config('stripe.publishable_key'),
                'payment_id' => $result['payment']->id,
                'transaction_id' => $result['payment']->transaction_id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect' => route('order.confirmation', $order->id),
            ]);
        } catch (\RuntimeException $e) {
            Log::warning('Stripe intent creation blocked: ' . $e->getMessage(), ['order_id' => $order->id]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            Log::error('Stripe intent creation failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Card payment could not be started. Please try again or use another payment method.'], 500);
        }
    }

    public function retry(PaymentRequest $request, StripePaymentService $stripe)
    {
        $limited = $this->ensureRateLimit($request);
        if ($limited) return $limited;

        $order = Order::with('payment')->findOrFail($request->order_id);

        if ($order->customer_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. This order does not belong to you.'], 403);
        }

        if ($order->payment_status === 'completed') {
            return response()->json(['success' => false, 'message' => 'This order has already been paid. No retry needed.'], 422);
        }

        try {
            $result = $stripe->retryPayment($order);
            return response()->json([
                'success' => true,
                'message' => 'New payment session created. Enter your card details to complete payment.',
                'client_secret' => $result['client_secret'],
                'publishable_key' => config('stripe.publishable_key'),
                'payment_id' => $result['payment']->id,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect' => route('order.confirmation', $order->id),
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe retry failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Retry failed. Please try again or use another payment method.'], 500);
        }
    }

    public function status(Request $request, Payment $payment, StripePaymentService $stripe)
    {
        if ($payment->order->customer_id !== auth()->id() && !auth()->user()?->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $data = $stripe->getPaymentStatus($payment);
            return response()->json(['success' => true, 'payment_id' => $payment->id, ...$data]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function confirm(PaymentRequest $request)
    {
        // PaymentRequest already normalizes "" -> null and validates required fields.
        $validated = $request->validated();
        $paymentIntentId = (string) ($validated['payment_intent_id'] ?? $request->input('payment_intent_id'));
        $paymentId = $validated['payment_id'] ?? null;
        $orderId = $validated['order_id'] ?? null;

        // 1) Try explicit payment_id
        $payment = null;
        if ($paymentId !== null) {
            $payment = Payment::with('order')->find($paymentId);
            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Payment not found.'], 404);
            }
        // 2) Fallback to order_id lookup
        } elseif ($orderId !== null) {
            $order = Order::with('payment')->find($orderId);
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
            if ((int) $order->customer_id !== (int) auth()->id() && !auth()->user()?->hasRole('admin')) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }
            $payment = $order->payment;
        // 3) Fallback: lookup by transaction_id (payment_intent_id) directly — handles nullable payment_id
        } else {
            $payment = Payment::with('order')->where('transaction_id', $paymentIntentId)->first();
        }

        if (!$payment) {
            // No payment record yet — webhook is source of truth, but confirm intent existence via Stripe if possible
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'payment_status' => 'pending',
                'redirect' => $orderId ? route('order.confirmation', $orderId) : null,
                'message' => 'No payment record found yet. Webhook will confirm shortly. Please refresh in a few seconds.',
            ]);
        }

        // Authorization: payment's order must belong to current user (or admin)
        if ((int) $payment->order->customer_id !== (int) auth()->id() && !auth()->user()?->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // Intent mismatch — but if payment was looked up by intent already, this is a no-op
        if ($payment->transaction_id !== $paymentIntentId) {
            Log::warning('Stripe confirm: intent mismatch', ['payment_id' => $payment->id, 'expected' => $payment->transaction_id, 'received' => $paymentIntentId]);
            // If we fell back via transaction lookup, this branch won't hit. Only when explicit ID mismatches.
            return response()->json(['success' => false, 'message' => 'Payment intent mismatch. Please retry payment.', 'retry' => true], 422);
        }

        return response()->json([
            'success' => true,
            'status' => $payment->status,
            'payment_status' => $payment->order->payment_status,
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'redirect' => route('order.confirmation', $payment->order_id),
            'message' => $payment->status === 'completed'
                ? 'Payment confirmed.'
                : 'Payment is being finalized. Redirecting to confirmation page — status will update automatically.',
        ]);
    }
}
