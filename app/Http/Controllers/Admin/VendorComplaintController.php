<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorComplaint;
use Illuminate\Http\Request;

class VendorComplaintController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorComplaint::with('vendor.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $complaints = $query->paginate(20)->withQueryString();
        return view('admin.vendor-complaints.index', compact('complaints'));
    }

    public function show(VendorComplaint $vendorComplaint)
    {
        $vendorComplaint->load(['vendor.user', 'reviewedBy']);
        return view('admin.vendor-complaints.show', compact('vendorComplaint'));
    }
}
