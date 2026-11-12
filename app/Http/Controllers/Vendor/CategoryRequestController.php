<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequestRequest;
use App\Models\VendorCategoryRequest;
use App\Repositories\VendorCategoryRequestRepository;
use App\Services\VendorCategoryRequestService;
use Exception;
use Illuminate\Support\Facades\Auth;

class CategoryRequestController extends Controller
{
    protected VendorCategoryRequestRepository $requestRepository;
    protected VendorCategoryRequestService $requestService;

    public function __construct(
        VendorCategoryRequestRepository $requestRepository,
        VendorCategoryRequestService $requestService
    ) {
        $this->requestRepository = $requestRepository;
        $this->requestService = $requestService;
    }

    // ===== VIEW ALL REQUESTS =====

    public function all()
    {
        $vendor = Auth::user()->vendor;

        $vendorCategories = \App\Models\Category::forVendor($vendor->id)
            ->active()
            ->with('parent')
            ->withCount('products')
            ->ordered()
            ->get();

        // Get vendor's category requests
        $vendorRequests = $this->requestRepository->getVendorRequests($vendor->id);

        return view('vendor.categories.all', compact('vendorCategories', 'vendorRequests'));
    }

    public function index()
    {
        $vendor = Auth::user()->vendor;
        $requests = $this->requestRepository->getVendorRequests($vendor->id);
        $pending = $this->requestRepository->getVendorPendingRequests($vendor->id)->count();

        return view('vendor.categories.requests', compact('requests', 'pending'));
    }

    // ===== CREATE REQUEST FORM =====

    public function create()
    {
        return view('vendor.categories.create-request');
    }

    // ===== STORE REQUEST =====

    public function store(CreateCategoryRequestRequest $request)
    {
        try {
            $vendor = Auth::user()->vendor;

            $this->requestService->createRequest(
                $vendor->id,
                $request->validated()
            );

            return redirect()->route('vendor.categories.requests')
                ->with('success', 'Category request submitted! Admins will review it soon.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    // ===== VIEW SINGLE REQUEST =====

    public function show(VendorCategoryRequest $request)
    {
        $vendor = Auth::user()->vendor;

        // Check authorization
        if ($request->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized');
        }

        return view('vendor.categories.request-detail', compact('request'));
    }
}