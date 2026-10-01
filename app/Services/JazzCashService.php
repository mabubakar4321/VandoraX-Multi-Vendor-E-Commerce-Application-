<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class JazzCashService
{
    protected $config;
    protected $merchantId;
    protected $password;
    protected $accountId;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->config = config('jazzcash');
        $this->merchantId = $this->config['merchant_id'];
        $this->password = $this->config['password'];
        $this->accountId = $this->config['account_id'];
        $this->secretKey = $this->config['secret_key'];

        $environment = $this->config['environment'];
        $this->baseUrl = $this->config['urls'][$environment];
    }

    /**
     * Initiate a JazzCash payment
     *
     * @param Order $order
     * @param string $mobileNumber
     * @return array
     * @throws Exception
     */
    public function initiatePayment(Order $order, $mobileNumber)
    {
        try {
            $amount = (int)($order->total_amount * 100); // Convert to smallest unit
            $referenceNumber = $this->generateReferenceNumber($order);
            $expiryDateTime = now()->addHours(24)->format('YmdHis');

            // Build request data
            $requestData = [
                'pp_Version' => '1.1',
                'pp_TxnType' => 'MWALLET',
                'pp_Language' => 'EN',
                'pp_MerchantID' => $this->merchantId,
                'pp_SubMerchantID' => '',
                'pp_Password' => $this->password,
                'pp_BankID' => '',
                'pp_ProductID' => '',
                'pp_TxnRefNo' => $referenceNumber,
                'pp_Amount' => $amount,
                'pp_TxnCurrency' => $this->config['currency'],
                'pp_TxnDateTime' => now()->format('YmdHis'),
                'pp_BillReference' => 'ORDER-' . $order->id,
                'pp_Description' => 'Order Payment - Order #' . $order->order_number,
                'pp_SecureHash' => '',
                'pp_ReturnURL' => $this->config['return_url'],
                'pp_NotifyURL' => $this->config['notify_url'],
                'pp_CustomerCardlessAccount' => $this->sanitizeMobileNumber($mobileNumber),
                'pp_CNIC' => '',
                'pp_CustomerEmail' => auth()->user()->email,
                'pp_CustomerMobile' => $this->sanitizeMobileNumber($mobileNumber),
                'pp_CustomerName' => auth()->user()->name,
                'pp_CustomerID' => auth()->id(),
                'pp_ExpiryDateTime' => $expiryDateTime,
            ];

            // Generate secure hash
            $requestData['pp_SecureHash'] = $this->generateSecureHash($requestData);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'currency' => $this->config['currency'],
                'payment_method' => 'wallet',
                'payment_gateway' => 'jazzcash',
                'transaction_id' => $referenceNumber,
                'status' => 'pending',
                'escrow_status' => 'held',
                'gateway_response' => [
                    'reference_number' => $referenceNumber,
                    'mobile_number' => $this->sanitizeMobileNumber($mobileNumber),
                    'initiated_at' => now()->toIso8601String(),
                ],
            ]);

            Log::info('JazzCash payment initiated', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'reference_number' => $referenceNumber,
            ]);

            // Store redirect URL in gateway_response for retrieval
            $payment->update([
                'gateway_response' => array_merge($payment->gateway_response ?? [], [
                    'redirect_url' => $this->getPaymentRedirectUrl($requestData),
                    'request_data' => $requestData,
                ]),
            ]);

            return $payment;
        } catch (Exception $e) {
            Log::error('JazzCash payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle JazzCash callback/webhook
     *
     * @param array $data
     * @return array
     */
    public function handleCallback($data)
    {
        try {
            // Verify secure hash
            if (!$this->verifySecureHash($data)) {
                Log::warning('JazzCash callback hash verification failed', $data);
                return [
                    'success' => false,
                    'message' => 'Hash verification failed',
                ];
            }

            $transactionId = $data['pp_TxnRefNo'] ?? null;
            $status = $data['pp_ResponseCode'] ?? null;
            $description = $data['pp_ResponseDesc'] ?? null;

            // Find payment by transaction ID
            $payment = Payment::where('transaction_id', $transactionId)->first();

            if (!$payment) {
                Log::warning('JazzCash callback: Payment not found', [
                    'transaction_id' => $transactionId,
                ]);
                return [
                    'success' => false,
                    'message' => 'Payment not found',
                ];
            }

            // Update payment status
            if ($status === '0') {
                // Success
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $data),
                ]);

                // Update order payment status
                $payment->order->update([
                    'payment_status' => 'completed',
                    'payment_id' => $payment->id,
                ]);

                Log::info('JazzCash payment completed', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment completed successfully',
                    'payment_id' => $payment->id,
                ];
            } else {
                // Failed
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], $data),
                ]);

                Log::warning('JazzCash payment failed', [
                    'payment_id' => $payment->id,
                    'status_code' => $status,
                    'description' => $description,
                ]);

                return [
                    'success' => false,
                    'message' => $description,
                    'payment_id' => $payment->id,
                ];
            }
        } catch (Exception $e) {
            Log::error('JazzCash callback processing failed', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            return [
                'success' => false,
                'message' => 'Callback processing failed',
            ];
        }
    }

    /**
     * Query transaction status
     *
     * @param string $transactionId
     * @return array
     */
    public function queryTransaction($transactionId)
    {
        try {
            $requestData = [
                'pp_Version' => '1.1',
                'pp_TxnType' => 'MWALLET',
                'pp_Language' => 'EN',
                'pp_MerchantID' => $this->merchantId,
                'pp_Password' => $this->password,
                'pp_BankID' => '',
                'pp_SubMerchantID' => '',
                'pp_TxnRefNo' => $transactionId,
                'pp_TxnDateTime' => now()->format('YmdHis'),
            ];

            // Add secure hash
            $requestData['pp_SecureHash'] = $this->generateSecureHash($requestData);

            // Make API request
            $response = Http::timeout($this->config['timeout'])
                ->post($this->baseUrl . $this->config['endpoints']['query'], $requestData);

            if ($response->failed()) {
                throw new Exception('Query failed: ' . $response->body());
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (Exception $e) {
            Log::error('JazzCash transaction query failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate a unique reference number
     *
     * @param Order $order
     * @return string
     */
    protected function generateReferenceNumber(Order $order)
    {
        return 'JZ-' . $order->id . '-' . time();
    }

    /**
     * Generate secure hash for request
     *
     * @param array $data
     * @return string
     */
    protected function generateSecureHash($data)
    {
        $hashString = '';

        // Build hash string in specific order
        $hashFields = [
            'pp_Amount',
            'pp_BankID',
            'pp_BillReference',
            'pp_Description',
            'pp_Language',
            'pp_MerchantID',
            'pp_Password',
            'pp_TxnCurrency',
            'pp_TxnDateTime',
            'pp_TxnRefNo',
            'pp_TxnType',
            'pp_Version',
        ];

        foreach ($hashFields as $field) {
            if (isset($data[$field])) {
                $hashString .= $data[$field] . '&';
            }
        }

        $hashString .= $this->secretKey;

        return hash_hmac('sha256', $hashString, $this->secretKey);
    }

    /**
     * Verify secure hash from response
     *
     * @param array $data
     * @return bool
     */
    protected function verifySecureHash($data)
    {
        if (!isset($data['pp_SecureHash'])) {
            return false;
        }

        $receivedHash = $data['pp_SecureHash'];

        // Build hash string for verification
        $hashString = '';
        $hashFields = [
            'pp_Amount',
            'pp_BankID',
            'pp_BillReference',
            'pp_Description',
            'pp_Language',
            'pp_MerchantID',
            'pp_Password',
            'pp_ResponseCode',
            'pp_ResponseDesc',
            'pp_TxnCurrency',
            'pp_TxnDateTime',
            'pp_TxnRefNo',
            'pp_TxnType',
            'pp_Version',
        ];

        foreach ($hashFields as $field) {
            if (isset($data[$field])) {
                $hashString .= $data[$field] . '&';
            }
        }

        $hashString .= $this->secretKey;

        $calculatedHash = hash_hmac('sha256', $hashString, $this->secretKey);

        return hash_equals($calculatedHash, $receivedHash);
    }

    /**
     * Get payment redirect URL
     *
     * @param array $requestData
     * @return string
     */
    protected function getPaymentRedirectUrl($requestData)
    {
        // JazzCash expects form submission to complete payment
        return $this->baseUrl . $this->config['endpoints']['complete'];
    }

    /**
     * Sanitize mobile number to JazzCash format
     *
     * @param string $mobileNumber
     * @return string
     */
    protected function sanitizeMobileNumber($mobileNumber)
    {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $mobileNumber);

        // Ensure it starts with 92 (Pakistan country code) or convert from 0
        if (substr($cleaned, 0, 2) === '03') {
            $cleaned = '92' . substr($cleaned, 1);
        } elseif (substr($cleaned, 0, 1) === '3') {
            $cleaned = '92' . $cleaned;
        } elseif (substr($cleaned, 0, 2) !== '92') {
            // Add 92 if not present
            $cleaned = '92' . substr($cleaned, -10);
        }

        return $cleaned;
    }
}
