<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerComplaint;
use Illuminate\Http\Request;

class CustomerComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerComplaint::with('customer')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $complaints = $query->paginate(20)->withQueryString();
        return view('admin.customer-complaints.index', compact('complaints'));
    }

    public function show(CustomerComplaint $customerComplaint)
    {
        $customerComplaint->load(['customer', 'reviewedBy']);
        return view('admin.customer-complaints.show', compact('customerComplaint'));
    }
}
