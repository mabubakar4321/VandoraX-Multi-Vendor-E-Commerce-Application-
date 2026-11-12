<?php

namespace App\Http\Controllers;

use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripePaymentService $stripePayments)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('stripe.webhook_secret');

        // Placeholder keys mean not configured — reject silently in production
        $isPlaceholder = !$secret || str_contains($secret, 'placeholder');

        try {
            if ($isPlaceholder) {
                // In local/dev without real secret, allow debug bypass
                if (!config('app.debug')) {
                    return response()->json(['message' => 'Webhook not configured'], 503);
                }
                $event = json_decode($payload, false, 512, JSON_THROW_ON_ERROR);
                if (empty($event->type)) {
                    return response()->json(['message' => 'Invalid payload'], 400);
                }
            } else {
                if (!$signature) {
                    return response()->json(['message' => 'Missing Stripe-Signature'], 400);
                }
                $event = Webhook::constructEvent($payload, $signature, $secret, config('stripe.webhook_tolerance', 300));
            }

            $stripePayments->handleWebhookEvent($event);

            return response()->json(['received' => true]);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid signature'], 400);
        } catch (\Throwable $exception) {
            Log::error('Stripe webhook handling failed', ['message' => $exception->getMessage()]);
            return response()->json(['message' => 'Webhook error'], 400);
        }
    }
}
