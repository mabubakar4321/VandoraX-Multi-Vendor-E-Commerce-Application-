<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\JazzCashService;
use App\Services\BankTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $jazzcashService;
    protected $bankTransferService;

    public function __construct(
        JazzCashService $jazzcashService,
        BankTransferService $bankTransferService
    ) {
        $this->jazzcashService = $jazzcashService;
        $this->bankTransferService = $bankTransferService;
    }

    /**
     * Initiate JazzCash payment
     */
    public function initiateJazzCashPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'mobile_number' => 'required|string|regex:/^(\+92|0|92)?3\d{9}$/',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            // Verify order belongs to user
            if ($order->customer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Initiate payment
            $result = $this->jazzcashService->initiatePayment($order, $request->mobile_number);

            if ($result['success']) {
                return response()->json($result);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment',
            ], 400);
        } catch (\Exception $e) {
            Log::error('JazzCash payment initiation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed',
            ], 500);
        }
    }

    /**
     * Handle JazzCash callback/webhook
     */
    public function handleJazzCashCallback(Request $request)
    {
        try {
            $data = $request->all();

            Log::info('JazzCash callback received', $data);

            $result = $this->jazzcashService->handleCallback($data);

            if ($result['success']) {
                // Redirect to success page
                $payment = Payment::find($result['payment_id']);
                return redirect()->route('order.confirmation', $payment->order_id)
                    ->with('success', 'Payment completed successfully!');
            }

            // Redirect to failure page
            return redirect()->route('checkout.index')
                ->with('error', $result['message']);
        } catch (\Exception $e) {
            Log::error('JazzCash callback handling failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment processing failed');
        }
    }

    /**
     * Initiate bank transfer
     */
    public function initiateBankTransfer(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);

            // Verify order belongs to user
            if ($order->customer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Initiate bank transfer
            $result = $this->bankTransferService->initiatePayment(
                $order,
                $request->transaction_reference
            );

            if ($result['success']) {
                return response()->json($result);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate bank transfer',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Bank transfer initiation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bank transfer initiation failed',
            ], 500);
        }
    }

    /**
     * Verify bank transfer
     */
    public function verifyBankTransfer(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'customer_reference' => 'required|string|max:100',
        ]);

        try {
            $payment = Payment::findOrFail($request->payment_id);

            // Verify payment belongs to user
            if ($payment->order->customer_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Verify transfer
            $result = $this->bankTransferService->verifyTransfer(
                $payment,
                $request->customer_reference
            );

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Bank transfer verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
            ], 500);
        }
    }

    /**
     * Get bank details
     */
    public function getBankDetails()
    {
        try {
            $details = $this->bankTransferService->getBankDetails();
            $instructions = $this->bankTransferService->getTransferInstructions();

            return response()->json([
                'success' => true,
                'bank_details' => $details,
                'instructions' => $instructions,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get bank details', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get bank details',
            ], 500);
        }
    }

    /**
     * Validate payment method
     */
    public function validatePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:jazzcash,bank_transfer,cod',
            'mobile_number' => 'required_if:payment_method,jazzcash|nullable',
        ]);

        $method = $request->payment_method;

        try {
            if ($method === 'jazzcash') {
                // Validate mobile number format
                if (!preg_match('/^(\+92|0|92)?3\d{9}$/', $request->mobile_number)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid mobile number format',
                    ], 400);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Mobile number valid',
                ]);
            } elseif ($method === 'bank_transfer') {
                $bankDetails = $this->bankTransferService->getBankDetails();

                return response()->json([
                    'success' => true,
                    'bank_details' => $bankDetails,
                ]);
            } elseif ($method === 'cod') {
                return response()->json([
                    'success' => true,
                    'message' => 'Cash on delivery selected',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Payment method validation failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
            ], 500);
        }
    }
}
