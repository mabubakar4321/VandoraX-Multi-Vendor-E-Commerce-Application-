<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\VendorBankDetailRepository;
use App\Repositories\VendorRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VendorAuthService extends AuthService
{
    protected VendorRepository $vendorRepository;
    protected VendorBankDetailRepository $bankRepository;

    public function __construct(
        UserRepository $userRepository,
        VendorRepository $vendorRepository,
        VendorBankDetailRepository $bankRepository
    ) {
        parent::__construct($userRepository);
        $this->vendorRepository = $vendorRepository;
        $this->bankRepository   = $bankRepository;
    }

    /* =================================================================
     | STEP 1 — Basic info. Creates the user + sends verification email.
     |=================================================================*/
    public function registerVendorStep1(array $data): array
    {
        if ($this->userRepository->emailExists($data['email'])) {
            return $this->fail('This email is already registered.');
        }

        if (! empty($data['phone_number']) && $this->userRepository->phoneExists($data['phone_number'])) {
            return $this->fail('This phone number is already registered.');
        }

        return DB::transaction(function () use ($data) {
            $user = $this->createUser([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'password'          => $data['password'],
                'phone_number'      => $data['phone_number'],
                'is_active'         => true,
                'status'            => 'pending',    // pending until admin approves the store
                'email_verified_at' => null,
            ]);

            $user->assignRole('vendor');

            // Laravel builds a signed, expiring URL and mails it (see Section 11)
            $user->sendEmailVerificationNotification();

            return $this->ok(
                'Step 1 complete. We sent a verification link to ' . $user->email,
                ['user' => $user]
            );
        });
    }

    /* =================================================================
     | STEP 2 — Bank details
     |=================================================================*/
    public function registerVendorStep2($userId, array $data): array
    {
        $user = $this->userRepository->find($userId);

        if (! $user) {
            return $this->fail('Registration session expired. Please start again.', 'no_user');
        }

        /*
         * NOTHING IS WRITTEN TO THE DATABASE HERE.
         *
         * vendor_bank_details.vendor_id is required, and the vendors row does
         * not exist until step 3. So we validate + normalise the data and hand
         * it back; the controller parks it in the session and step 3 writes it
         * in the SAME transaction as the vendor. See 3.4.2.
         */
        $bankData = [
            'account_holder_name' => $data['account_holder_name'],
            'account_number'      => $data['account_number'],
            'iban'                => $data['iban'] ?? null,
            'bank_name'           => $data['bank_name'],
            'account_type'        => $data['account_type'],
            'is_primary'          => true,
            'is_verified'         => false,   // admin verifies the payout account later
        ];

        return $this->ok('Bank details saved.', [
            'bank_data'      => $bankData,
            'email_verified' => (bool) $user->email_verified_at,   // controller can warn
        ]);
    }

    /** Used in Phase 4 when an approved vendor edits their payout account */
    public function updateBankDetails($vendorId, array $data): array
    {
        $this->bankRepository->updateOrCreateForVendor($vendorId, $data + [
            'vendor_id'   => $vendorId,
            'is_primary'  => true,
            'is_verified' => false,   // re-verification required after any change
        ]);

        return $this->ok('Payout account updated. It will be re-verified before your next payout.');
    }

    /* =================================================================
     | STEP 3 — Store info + documents. Finishes registration.
     |=================================================================*/
    public function registerVendorStep3($userId, array $data, array $bankData = []): array
    {
        $user = $this->userRepository->find($userId);

        if (! $user) {
            return $this->fail('Registration session expired. Please start again.', 'no_user');
        }

        if ($this->vendorRepository->findByUserId($userId)) {
            return $this->fail('You have already submitted a store application.', 'duplicate');
        }

        if ($this->vendorRepository->storeNameExists($data['store_name'])) {
            return $this->fail('That store name is taken. Please choose another.');
        }

        if (empty($bankData)) {
            return $this->fail('Your bank details are missing. Please redo step 2.', 'no_bank');
        }

        return DB::transaction(function () use ($userId, $data, $bankData) {
            $vendor = $this->vendorRepository->create([
                'user_id'           => $userId,
                'store_name'        => $data['store_name'],
                'store_slug'        => $this->vendorRepository->generateUniqueSlug(Str::slug($data['store_name'])),
                'store_description' => $data['store_description'] ?? null,
                'category_id'       => $data['category_id'],
                'status'            => 'pending',      // awaiting admin approval
                'is_verified'       => false,
            ]);

            // NOW the vendor_id exists — write the bank details carried from step 2
            $vendor->bankDetails()->create($bankData);

            foreach ($data['documents'] ?? [] as $doc) {
                $vendor->documents()->create([
                    'document_type' => $doc['type'],
                    'file_path'     => $doc['path'],
                    'file_name'     => $doc['name'],
                    'status'        => 'pending',
                ]);
            }

            return $this->ok(
                'Application submitted. Our team will review it within 2 business days.',
                ['vendor' => $vendor]
            );
        });
    }

    /* =================================================================
     | LOGIN
     |=================================================================*/
    public function loginVendor(string $email, string $password, bool $remember = false): array
    {
        $check = $this->validateLogin($email, $password);

        if (! $check['success']) {
            return $check;
        }

        /** @var User $user */
        $user = $check['user'];

        if (! $user->hasRole('vendor')) {
            return $this->fail('These credentials do not match our records.', 'wrong_portal');
        }

        // Email verification is enforced HERE (not during registration)
        if (! $user->email_verified_at) {
            return $this->fail(
                'Please verify your email address first. Check your inbox for the link.',
                'unverified',
                ['user' => $user]      // controller uses this to offer "resend"
            );
        }

        $vendor = $this->vendorRepository->findByUserId($user->id);

        // Registration never finished — log them in and send them back to step 3
        if (! $vendor) {
            $this->startSession($user, $remember);
            return $this->ok('Please complete your store application.', [
                'user'     => $user,
                'redirect' => 'vendor.register.step3',
            ]);
        }

        // Pending or rejected: allow login, but land them on the status page
        if ($vendor->status !== 'active') {
            $this->startSession($user, $remember);
            return $this->ok('Your store is not approved yet.', [
                'user'     => $user,
                'vendor'   => $vendor,
                'redirect' => 'vendor.pending',
            ]);
        }

        $this->startSession($user, $remember);

        return $this->ok('Welcome back, ' . $vendor->store_name, [
            'user'     => $user,
            'vendor'   => $vendor,
            'redirect' => 'vendor.dashboard',
        ]);
    }

    public function logoutVendor(): array
    {
        return $this->endSession();
    }

    /** Used by the admin panel in Phase 4 */
    public function approveVendor($vendorId): array
    {
        $vendor = $this->vendorRepository->approveVendor($vendorId);

        if (! $vendor) {
            return $this->fail('Vendor not found.');
        }

        $this->userRepository->update($vendor->user_id, ['status' => 'active']);
        $vendor->user->notify(new \App\Notifications\VendorApprovedNotification($vendor));

        return $this->ok('Vendor approved.', ['vendor' => $vendor]);
    }

    public function rejectVendor($vendorId, string $reason): array
    {
        $vendor = $this->vendorRepository->rejectVendor($vendorId, $reason);

        if (! $vendor) {
            return $this->fail('Vendor not found.');
        }

        $vendor->user->notify(new \App\Notifications\VendorRejectedNotification($vendor, $reason));

        return $this->ok('Vendor rejected.', ['vendor' => $vendor]);
    }
}