<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class BankTransferService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('bank-transfer');
    }

    /**
     * Initiate a bank transfer payment
     *
     * @param Order $order
     * @param string|null $transactionReference
     * @return array
     * @throws Exception
     */
    public function initiatePayment(Order $order, $transactionReference = null)
    {
        try {
            $referenceNumber = $this->generateReferenceNumber($order);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'currency' => $this->config['currency'],
                'payment_method' => 'bank_transfer',
                'payment_gateway' => 'jazzcash', // Using as default gateway for bank transfers
                'transaction_id' => $referenceNumber,
                'status' => 'pending',
                'escrow_status' => 'held',
                'gateway_response' => [
                    'reference_number' => $referenceNumber,
                    'bank_account' => $this->config['bank'],
                    'initiated_at' => now()->toIso8601String(),
                    'customer_transaction_reference' => $transactionReference,
                ],
            ]);

            // Update order with payment info
            $order->update([
                'payment_id' => $payment->id,
            ]);

            // Send bank details email to customer
            $this->sendBankDetailsEmail($order, $payment);

            // Notify admin
            if ($this->config['notifications']['notify_admin']) {
                $this->notifyAdminBankTransferPending($order, $payment);
            }

            Log::info('Bank transfer payment initiated', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'reference_number' => $referenceNumber,
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'reference_number' => $referenceNumber,
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'bank_details' => $this->config['bank'],
                'message' => 'Bank transfer initiated. Please transfer the amount to the provided account.',
            ];
        } catch (Exception $e) {
            Log::error('Bank transfer payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verify bank transfer
     *
     * @param Payment $payment
     * @param string $customerReference
     * @return array
     */
    public function verifyTransfer($payment, $customerReference)
    {
        try {
            // Update payment with customer's transaction reference
            $payment->update([
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'customer_reference' => $customerReference,
                    'verification_requested_at' => now()->toIso8601String(),
                ]),
            ]);

            // Check if manual verification is enabled
            if ($this->config['verification']['manual_review']) {
                $payment->update(['status' => 'processing']);

                // Notify admin for manual verification
                $this->notifyAdminForVerification($payment);

                return [
                    'success' => true,
                    'message' => 'Transfer verification submitted. Our team will verify and process your order shortly.',
                    'status' => 'under_review',
                ];
            } else {
                // Auto-verify (if enabled)
                if ($this->config['verification']['enabled']) {
                    $payment->markAsCompleted();
                    $payment->order->update(['payment_status' => 'completed']);

                    return [
                        'success' => true,
                        'message' => 'Payment verified successfully!',
                        'status' => 'completed',
                    ];
                }
            }

            return [
                'success' => true,
                'message' => 'Transfer verification submitted.',
            ];
        } catch (Exception $e) {
            Log::error('Bank transfer verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Verification failed. Please try again.',
            ];
        }
    }

    /**
     * Manually verify and complete transfer (admin action)
     *
     * @param Payment $payment
     * @param string $adminNotes
     * @return array
     */
    public function adminVerifyTransfer($payment, $adminNotes = null)
    {
        try {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'admin_verified_at' => now()->toIso8601String(),
                    'admin_notes' => $adminNotes,
                ]),
            ]);

            // Release from escrow
            $payment->releaseFromEscrow();

            // Update order
            $payment->order->update([
                'payment_status' => 'completed',
            ]);

            // Send confirmation email
            $this->sendPaymentConfirmationEmail($payment->order);

            Log::info('Bank transfer manually verified by admin', [
                'payment_id' => $payment->id,
                'order_id' => $payment->order_id,
            ]);

            return [
                'success' => true,
                'message' => 'Payment verified and order confirmed',
            ];
        } catch (Exception $e) {
            Log::error('Admin bank transfer verification failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Verification failed',
            ];
        }
    }

    /**
     * Reject bank transfer
     *
     * @param Payment $payment
     * @param string $reason
     * @return array
     */
    public function rejectTransfer($payment, $reason)
    {
        try {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'rejected_at' => now()->toIso8601String(),
                    'rejection_reason' => $reason,
                ]),
            ]);

            // Refund if amount was received
            $payment->refund($reason);

            // Update order status
            $payment->order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);

            Log::info('Bank transfer rejected', [
                'payment_id' => $payment->id,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Transfer rejected and refund initiated',
            ];
        } catch (Exception $e) {
            Log::error('Bank transfer rejection failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Rejection failed',
            ];
        }
    }

    /**
     * Get bank details for display
     *
     * @return array
     */
    public function getBankDetails()
    {
        return $this->config['bank'];
    }

    /**
     * Get all bank accounts
     *
     * @return array
     */
    public function getAllBankAccounts()
    {
        $accounts = [$this->config['bank']];

        if (!empty($this->config['additional_accounts'])) {
            $accounts = array_merge($accounts, $this->config['additional_accounts']);
        }

        return $accounts;
    }

    /**
     * Get transfer instructions
     *
     * @return array
     */
    public function getTransferInstructions()
    {
        return $this->config['instructions'];
    }

    /**
     * Generate unique reference number
     *
     * @param Order $order
     * @return string
     */
    protected function generateReferenceNumber(Order $order)
    {
        return 'BT-' . $order->id . '-' . time();
    }

    /**
     * Send bank details email to customer
     *
     * @param Order $order
     * @param Payment $payment
     */
    protected function sendBankDetailsEmail($order, $payment)
    {
        try {
            $data = [
                'order' => $order,
                'payment' => $payment,
                'bank_details' => $this->config['bank'],
                'instructions' => $this->config['instructions'],
                'amount' => $order->total_amount,
                'reference' => $payment->transaction_id,
            ];

            Mail::send('mails.bank-transfer-details', $data, function ($message) use ($order) {
                $message->to($order->customer->email)
                        ->subject('Bank Transfer Details - Order #' . $order->order_number);
            });
        } catch (Exception $e) {
            Log::error('Failed to send bank details email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send payment confirmation email
     *
     * @param Order $order
     */
    protected function sendPaymentConfirmationEmail($order)
    {
        try {
            Mail::send('mails.payment-confirmation', [
                'order' => $order,
                'payment_method' => 'Bank Transfer',
            ], function ($message) use ($order) {
                $message->to($order->customer->email)
                        ->subject('Payment Confirmed - Order #' . $order->order_number);
            });
        } catch (Exception $e) {
            Log::error('Failed to send payment confirmation email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify admin of pending bank transfer
     *
     * @param Order $order
     * @param Payment $payment
     */
    protected function notifyAdminBankTransferPending($order, $payment)
    {
        try {
            $adminEmail = $this->config['notifications']['admin_email'];

            Mail::send('mails.admin-bank-transfer-pending', [
                'order' => $order,
                'payment' => $payment,
                'bank_details' => $this->config['bank'],
                'amount' => $order->total_amount,
            ], function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('[VandoraX] Pending Bank Transfer - Manual Verification Required');
            });
        } catch (Exception $e) {
            Log::error('Failed to notify admin of pending transfer', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify admin for manual verification
     *
     * @param Payment $payment
     */
    protected function notifyAdminForVerification($payment)
    {
        try {
            $adminEmail = $this->config['notifications']['admin_email'];

            Mail::send('mails.admin-transfer-verification-request', [
                'payment' => $payment,
                'order' => $payment->order,
                'customer_reference' => $payment->gateway_response['customer_reference'] ?? 'Not provided',
            ], function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('[VandoraX] Bank Transfer Verification Required - Order #' . $payment->order->order_number);
            });
        } catch (Exception $e) {
            Log::error('Failed to notify admin for verification', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
