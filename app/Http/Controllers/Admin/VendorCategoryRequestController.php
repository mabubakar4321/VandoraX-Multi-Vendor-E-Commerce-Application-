<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorCategoryRequest;
use App\Repositories\VendorCategoryRequestRepository;
use App\Services\VendorCategoryRequestService;
use Exception;
use Illuminate\Http\Request;

class VendorCategoryRequestController extends Controller
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

    // ===== LIST PENDING REQUESTS =====

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $query = $request->get('q');

        if ($query) {
            $requests = $this->requestRepository->search($query);
        } else {
            $requests = $this->requestRepository->getByStatusPaginated($status, 15);
        }

        $statistics = $this->requestService->getStatistics();

        return view('admin.category-requests.index', compact('requests', 'statistics', 'status', 'query'));
    }

    // ===== VIEW SINGLE REQUEST =====

    public function show(VendorCategoryRequest $request)
    {
        $request->load(['vendor', 'vendor.user', 'approvedBy']);

        return view('admin.category-requests.show', compact('request'));
    }

    // ===== APPROVE REQUEST =====

    public function approve(Request $request, VendorCategoryRequest $categoryRequest)
    {
        $request->validate([
            'comment' => 'nullable|string|max:500',
        ]);

        try {
            $this->requestService->approveRequest(
                $categoryRequest->id,
                auth()->id(),
                $request->comment
            );

            return redirect()->back()
                ->with('success', 'Category request approved! Category has been created.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ===== REJECT REQUEST =====

    public function reject(Request $request, VendorCategoryRequest $categoryRequest)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->requestService->rejectRequest(
                $categoryRequest->id,
                auth()->id(),
                $request->reason
            );

            return redirect()->back()
                ->with('success', 'Category request rejected.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ===== BULK ACTIONS =====

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:vendor_category_requests,id',
        ]);

        try {
            $approved = 0;

            foreach ($request->ids as $id) {
                $categoryRequest = VendorCategoryRequest::findOrFail($id);
                if ($categoryRequest->isPending()) {
                    $this->requestService->approveRequest(
                        $id,
                        auth()->id()
                    );
                    $approved++;
                }
            }

            return redirect()->back()
                ->with('success', "Approved {$approved} request(s)!");
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:vendor_category_requests,id',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $rejected = 0;

            foreach ($request->ids as $id) {
                $categoryRequest = VendorCategoryRequest::findOrFail($id);
                if ($categoryRequest->isPending()) {
                    $this->requestService->rejectRequest(
                        $id,
                        auth()->id(),
                        $request->reason
                    );
                    $rejected++;
                }
            }

            return redirect()->back()
                ->with('success', "Rejected {$rejected} request(s)!");
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}