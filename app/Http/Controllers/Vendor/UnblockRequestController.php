<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorUnblockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UnblockRequestSubmittedNotification;

class UnblockRequestController extends Controller
{
    public function create()
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            return redirect()->route('vendor.dashboard')
                ->with('error', 'No vendor profile found.');
        }

        $existingRequest = VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->pending()
            ->first();

        if ($existingRequest) {
            return redirect()->route('vendor.unblock.show', $existingRequest)
                ->with('info', 'You already have a pending unblock request.');
        }

        return view('vendor.unblock.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        $vendor = Auth::user()->vendor;

        if (!$vendor) {
            abort(403, 'No vendor profile found.');
        }

        $existingRequest = VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->pending()
            ->exists();

        if ($existingRequest) {
            return back()->with('error', 'You already have a pending unblock request.');
        }

        $validated = $request->validate([
            'improvements' => 'required|string|min:50|max:5000',
            'reason_to_unblock' => 'required|string|min:50|max:5000',
            'evidence_files' => 'nullable|array|max:5',
            'evidence_files.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $reason = "Improvements made:\n{$validated['improvements']}\n\nReason to unblock:\n{$validated['reason_to_unblock']}";

        $evidenceFiles = [];
        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $evidenceFiles[] = $file->store('unblock-evidence', 'public');
            }
        }

        $unblockRequest = VendorUnblockRequest::create([
            'vendor_id' => $vendor->id,
            'improvements' => $validated['improvements'],
            'reason_to_unblock' => $validated['reason_to_unblock'],
            'evidence_files' => $evidenceFiles ?: null,
            'reason' => $reason,
            'status' => 'pending',
        ]);

        $vendor->update(['requested_unblock' => true]);

        $vendor->user?->notify(new UnblockRequestSubmittedNotification($unblockRequest));

        return redirect()->route('vendor.unblock.show', $unblockRequest)
            ->with('success', 'Unblock request submitted successfully. Our team will review it shortly.');
    }

    public function show(VendorUnblockRequest $unblockRequest)
    {
        $vendor = Auth::user()->vendor;

        if ($unblockRequest->vendor_id !== $vendor?->id) {
            abort(403);
        }

        return view('vendor.unblock.show', compact('unblockRequest'));
    }

    public function index()
    {
        $vendor = Auth::user()->vendor;
        $requests = VendorUnblockRequest::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(10);

        return view('vendor.unblock.index', compact('requests'));
    }
}
