<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRegisterStep1Request;
use App\Http\Requests\VendorRegisterStep2Request;
use App\Http\Requests\VendorRegisterStep3Request;
use App\Services\VendorRegistrationService;
use App\Services\DocumentUploadService;
use App\Models\Category;
use Illuminate\Http\Request;

class VendorRegistrationController extends Controller
{
    protected $registrationService;
    protected $documentService;

    public function __construct(
        VendorRegistrationService $registrationService,
        DocumentUploadService $documentService
    ) {
        $this->registrationService = $registrationService;
        $this->documentService = $documentService;
    }

    // ===== STEP 1: BASIC INFO =====

    /**
     * Show step 1 form
     */
    public function showStep1Form()
    {
        return view('auth.vendor.register-step1');  // ✅ FIXED
    }

    /**
     * Handle step 1 submission
     */
    public function submitStep1(VendorRegisterStep1Request $request)
    {
        $result = $this->registrationService->registerStep1($request->validated());

        if (!$result['success']) {
            return back()
                ->withInput($request->only(['name', 'email', 'phone_number']))
                ->withErrors(['message' => $result['message']]);
        }

        // Store user ID in session for next steps
        session(['vendor_registration_user_id' => $result['user']->id]);

        return redirect()
            ->route('verification.notice')
            ->with('pending_email', $result['user']->email)
            ->with('success', 'Step 1 complete. Please verify your email to continue.');
    }

    // ===== STEP 2: BANK DETAILS =====

    /**
     * Show step 2 form
     */
    public function showStep2Form()
    {
        // Redirect to step 1 if user ID not in session
        if (!session('vendor_registration_user_id')) {
            return redirect()->route('vendor.register.step1');
        }

        $banks = [
            'hbl' => 'HBL (Habib Bank Limited)',
            'ubl' => 'UBL (United Bank Limited)',
            'nbp' => 'NBP (National Bank of Pakistan)',
            'alfalah' => 'Bank Alfalah',
            'habib' => 'Bank Habib',
            'fia' => 'FIA Bank',
            'summit' => 'Summit Bank',
            'meezan' => 'Meezan Bank',
            'askari' => 'Askari Bank',
            'faysal' => 'Faysal Bank',
            'burj' => 'Burj Bank',
        ];

        return view('auth.vendor.register-step2', compact('banks'));  // ✅ FIXED
    }

    /**
     * Handle step 2 submission
     */
    public function submitStep2(VendorRegisterStep2Request $request)
    {
        $userId = session('vendor_registration_user_id');

        if (!$userId) {
            return redirect()->route('vendor.register.step1');
        }

        $result = $this->registrationService->registerStep2($userId, $request->validated());

        if (!$result['success']) {
            return back()
                ->withInput($request->except('account_number', 'iban'))
                ->withErrors(['message' => $result['message']]);
        }

        return redirect()
            ->route('vendor.register.step3')
            ->with('success', 'Step 2 Complete! Now upload your documents.');
    }

    // ===== STEP 3: DOCUMENTS =====

    /**
     * Show step 3 form
     */
    public function showStep3Form()
    {
        if (!session('vendor_registration_user_id')) {
            return redirect()->route('vendor.register.step1');
        }

        $categories = Category::active()->get();
        $documentRequirements = [
            'cnic' => 'CNIC (Front or combined image)',
            'tax_certificate' => 'Tax Certificate (PDF)',
            'bank_proof' => 'Bank Proof (Statement or Passbook)',
        ];

        return view('auth.vendor.register-step3', compact('categories', 'documentRequirements'));  // ✅ FIXED
    }

    /**
     * Handle step 3 submission
     */
    public function submitStep3(VendorRegisterStep3Request $request)
    {
        $userId = session('vendor_registration_user_id');

        if (!$userId) {
            return redirect()->route('vendor.register.step1');
        }

        $data = $request->validated();
        $files = [];

        // Upload CNIC
        if ($request->hasFile('cnic_front')) {
            $cnicResult = $this->documentService->uploadDocument(
                $request->file('cnic_front'),
                $userId,
                'cnic_front'
            );

            if (!$cnicResult['success']) {
                return back()->withErrors(['cnic_front' => $cnicResult['message']]);
            }

            $files['cnic_front'] = $cnicResult['path'];
        }

        if ($request->hasFile('cnic_back')) {
            $cnicBackResult = $this->documentService->uploadDocument(
                $request->file('cnic_back'),
                $userId,
                'cnic_back'
            );

            if (!$cnicBackResult['success']) {
                return back()->withErrors(['cnic_back' => $cnicBackResult['message']]);
            }

            $files['cnic_back'] = $cnicBackResult['path'];
        }

        // Upload Bank Proof
        if ($request->hasFile('bank_proof')) {
            $bankResult = $this->documentService->uploadDocument(
                $request->file('bank_proof'),
                $userId,
                'bank_proof'
            );

            if (!$bankResult['success']) {
                return back()->withErrors(['bank_proof' => $bankResult['message']]);
            }

            $files['bank_proof'] = $bankResult['path'];
        }

        // Upload Tax Certificate (optional)
        if ($request->hasFile('tax_certificate')) {
            $taxResult = $this->documentService->uploadDocument(
                $request->file('tax_certificate'),
                $userId,
                'tax_certificate'
            );

            if (!$taxResult['success']) {
                return back()->withErrors(['tax_certificate' => $taxResult['message']]);
            }

            $files['tax_certificate'] = $taxResult['path'];
        }

        // Complete registration
        $result = $this->registrationService->registerStep3($userId, $data, $files);

        if (!$result['success']) {
            return back()->withErrors(['message' => $result['message']]);
        }

        // Clear session
        session()->forget('vendor_registration_user_id');

        return redirect()
            ->route('vendor.register.complete')
            ->with('success', 'Registration complete! Awaiting admin verification.');
    }

    /**
     * Registration complete page
     */
    public function complete()
    {
        return view('auth.vendor.register-complete');  // ✅ FIXED
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        $userId = $request->user_id;

        $result = $this->registrationService->resendVerificationEmail($userId);

        if (!$result['success']) {
            return back()->withErrors(['message' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Check registration progress
     */
    public function checkProgress()
    {
        $userId = session('vendor_registration_user_id');

        if (!$userId) {
            return response()->json(['error' => 'No registration in progress'], 400);
        }

        $progress = $this->registrationService->getRegistrationProgress($userId);

        return response()->json($progress);
    }
}