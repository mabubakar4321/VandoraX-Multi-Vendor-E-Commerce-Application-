<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\VendorRepository;
use App\Repositories\VendorBankDetailsRepository;
use App\Repositories\VendorDocumentRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class VendorRegistrationService
{
    protected $userRepository;
    protected $vendorRepository;
    protected $bankDetailsRepository;
    protected $documentRepository;

    public function __construct(
        UserRepository $userRepository,
        VendorRepository $vendorRepository,
        VendorBankDetailsRepository $bankDetailsRepository,
        VendorDocumentRepository $documentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->vendorRepository = $vendorRepository;
        $this->bankDetailsRepository = $bankDetailsRepository;
        $this->documentRepository = $documentRepository;
    }

    /**
     * STEP 1: Register basic information
     * Validates email, creates user, sends verification email
     */
    public function registerStep1(array $data)
    {
        // Validate email doesn't exist
        if ($this->userRepository->emailExists($data['email'])) {
            return [
                'success' => false,
                'message' => 'Email already registered',
                'user' => null,
            ];
        }

        // Validate phone doesn't exist (optional)
        if (isset($data['phone_number']) && $this->userRepository->findByPhone($data['phone_number'])) {
            return [
                'success' => false,
                'message' => 'Phone number already registered',
                'user' => null,
            ];
        }

        try {
            // Create user (email not verified yet)
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone_number' => $data['phone_number'] ?? null,
                'is_active' => true,
                'status' => 'active',
                'email_verified_at' => null, // Requires verification
            ]);

            // Assign vendor role
            $user->assignRole('vendor');

            // Send verification email
            $this->sendVerificationEmail($user);

            return [
                'success' => true,
                'message' => 'Registration initiated. Please verify your email.',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error creating account: ' . $e->getMessage(),
                'user' => null,
            ];
        }
    }

    /**
     * STEP 2: Save bank details
     */
    public function registerStep2($userId, array $data)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        // Check if email verified
        if (!$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email not verified yet. Please check your inbox.',
            ];
        }

        // Check if IBAN already used
        if ($this->bankDetailsRepository->ibanExists($data['iban'])) {
            return [
                'success' => false,
                'message' => 'This IBAN is already registered',
            ];
        }

        // Check if account already used
        if ($this->bankDetailsRepository->accountExists($data['account_number'])) {
            return [
                'success' => false,
                'message' => 'This account number is already registered',
            ];
        }

        try {
            // Get or create vendor record if not exists
            $vendor = $this->vendorRepository->findByUserId($userId);
            
            if (!$vendor) {
                // Create the vendor shell; store details are completed in step 3.
                $vendor = $this->vendorRepository->create([
                    'user_id' => $userId,
                    // Store details are collected in step 3, so use a temporary
                    // unique value until the vendor chooses a store name.
                    'store_name' => 'Pending Store ' . $userId,
                    'status' => 'pending',
                    'store_slug' => Str::slug($user->name . '-' . $userId),
                ]);
            }

            // Create bank details (encrypted)
            $bankData = array_merge($data, ['vendor_id' => $vendor->id]);
            $this->bankDetailsRepository->createEncrypted($bankData);

            return [
                'success' => true,
                'message' => 'Bank details saved successfully',
                'vendor' => $vendor,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saving bank details: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * STEP 3: Upload documents
     */
    public function registerStep3($userId, array $data, array $files = [])
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        // Check if email verified
        if (!$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email not verified',
            ];
        }

        $vendor = $this->vendorRepository->findByUserId($userId);

        if (!$vendor) {
            return [
                'success' => false,
                'message' => 'Vendor profile not found',
            ];
        }

        try {
            // Update vendor with store info
            $this->vendorRepository->update($vendor->id, [
                'store_name' => $data['store_name'],
                'store_slug' => Str::slug($data['store_name']),
                'store_description' => $data['store_description'] ?? null,
                'category_id' => $data['category_id'],
            ]);

            // Save documents
            if (isset($files) && count($files) > 0) {
                foreach ($files as $type => $filePath) {
                    $this->documentRepository->create([
                        'vendor_id' => $vendor->id,
                        'document_type' => $type,
                        'file_path' => $filePath,
                        'file_name' => basename($filePath),
                        'status' => 'pending',
                    ]);
                }
            }

            // Keep the vendor pending until an administrator approves the store.
            $this->vendorRepository->update($vendor->id, [
                'status' => 'pending',
            ]);

            // Send email to vendor
            $this->sendRegistrationCompleteEmail($user);

            // Send email to admin
            $this->sendAdminNotificationEmail($vendor);

            return [
                'success' => true,
                'message' => 'Registration complete! Awaiting admin verification.',
                'vendor' => $vendor->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error uploading documents: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send email verification link
     */
    public function sendVerificationEmail($user)
    {
        $user->sendEmailVerificationNotification();
    }

    /**
     * Send registration complete email
     */
    public function sendRegistrationCompleteEmail($user)
    {
        Mail::raw(
            'Your VandoraX vendor registration is complete and is now under review.',
            function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Registration Complete - Under Review');
            }
        );
    }

    /**
     * Send admin notification
     */
    public function sendAdminNotificationEmail($vendor)
    {
        $adminEmail = 'admin@vandorax.test';

        Mail::raw(
            'A new vendor registration requires approval for ' . $vendor->load('user')->user->email . '.',
            function ($message) use ($adminEmail) {
            $message->to($adminEmail)
                    ->subject('New Vendor Registration - Requires Approval');
            }
        );
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail($userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found',
            ];
        }

        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Email already verified',
            ];
        }

        $this->sendVerificationEmail($user);

        return [
            'success' => true,
            'message' => 'Verification email sent',
        ];
    }

    /**
     * Get registration progress
     */
    public function getRegistrationProgress($userId)
    {
        $user = $this->userRepository->find($userId);

        if (!$user) {
            return null;
        }

        $vendor = $this->vendorRepository->findByUserId($userId);

        return [
            'step1_complete' => (bool) $user,
            'email_verified' => (bool) $user->email_verified_at,
            'step2_complete' => (bool) ($vendor && $this->bankDetailsRepository->getByVendorId($vendor->id)),
            'step3_complete' => (bool) ($vendor && $vendor->status === 'pending'),
            'vendor_status' => $vendor?->status ?? null,
            'documents_uploaded' => $vendor ? $this->documentRepository->getByVendorId($vendor->id)->count() : 0,
        ];
    }
}