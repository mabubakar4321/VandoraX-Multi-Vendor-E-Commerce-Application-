<?php

namespace App\Http\Controllers;

use App\Models\CustomerComplaint;
use Illuminate\Http\Request;

class CustomerComplaintController extends Controller
{
    public function index()
    {
        $complaints = CustomerComplaint::where('customer_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('customer.complaints.index', compact('complaints'));
    }

    public function create()
    {
        return view('customer.complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $complaint = CustomerComplaint::create([
            ...$validated,
            'customer_id' => auth()->id(),
            'complaint_number' => 'CUS-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
        ]);

        return redirect()->route('customer.complaints.index')
            ->with('success', 'Complaint ' . $complaint->complaint_number . ' was sent to the admin team.');
    }
}
