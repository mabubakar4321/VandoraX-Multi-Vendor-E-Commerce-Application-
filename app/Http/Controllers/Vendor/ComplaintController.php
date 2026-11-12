<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorComplaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = VendorComplaint::where('vendor_id', auth()->user()->vendor->id)
            ->latest()
            ->paginate(12);

        return view('vendor.complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('vendor.complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $complaint = VendorComplaint::create([
            ...$validated,
            'vendor_id' => auth()->user()->vendor->id,
            'complaint_number' => 'CMP-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
        ]);

        return redirect()->route('vendor.complaints.index')
            ->with('success', 'Complaint ' . $complaint->complaint_number . ' was sent to the admin team.');
    }
}
