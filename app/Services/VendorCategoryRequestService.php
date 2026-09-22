<?php

namespace App\Services;

use App\Models\Category;
use App\Models\VendorCategoryRequest;
use App\Mail\CategoryRequestApprovedMail;
use App\Mail\CategoryRequestRejectedMail;
use App\Mail\CategoryRequestSubmittedMail;
use App\Repositories\VendorCategoryRequestRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class VendorCategoryRequestService
{
    protected VendorCategoryRequestRepository $requestRepository;

    public function __construct(VendorCategoryRequestRepository $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function createRequest(int $vendorId, array $data): VendorCategoryRequest
    {
        $request = DB::transaction(function () use ($vendorId, $data) {
            if ($this->requestRepository->hasPendingRequest($vendorId, $data['category_name'])) {
                throw new Exception('You already have a pending request for this category');
            }

            return $this->requestRepository->create([
                'vendor_id' => $vendorId,
                'category_name' => $data['category_name'],
                'description' => $data['description'] ?? null,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        });

        $request->load('vendor.user');

        try {
            Mail::send(new CategoryRequestSubmittedMail($request));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $request;
    }

    public function approveRequest(int $requestId, int $userId, ?string $comment = null): VendorCategoryRequest
    {
        [$request, $category] = DB::transaction(function () use ($requestId, $userId, $comment) {
            $request = $this->requestRepository->findOrFail($requestId);

            if (!$request->isPending()) {
                throw new Exception('Only pending requests can be approved');
            }

            $category = Category::create([
                'name' => $request->category_name,
                'slug' => Category::generateSlug($request->category_name),
                'description' => $request->description,
                'commission_rate' => 10.00,
                'is_active' => true,
                'created_by' => $userId,
            ]);

            $request->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $userId,
                'admin_comment' => $comment,
            ]);

            return [$request->fresh(['vendor.user']), $category];
        });

        try {
            Mail::send(new CategoryRequestApprovedMail($request, $category));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $request;
    }

    public function rejectRequest(int $requestId, int $userId, string $reason): VendorCategoryRequest
    {
        $request = DB::transaction(function () use ($requestId, $userId, $reason) {
            $request = $this->requestRepository->findOrFail($requestId);

            if (!$request->isPending()) {
                throw new Exception('Only pending requests can be rejected');
            }

            $request->update([
                'status' => 'rejected',
                'admin_comment' => $reason,
                'approved_by' => $userId,
            ]);

            return $request->fresh(['vendor.user']);
        });

        try {
            Mail::send(new CategoryRequestRejectedMail($request));
        } catch (Throwable $exception) {
            report($exception);
        }

        return $request;
    }

    public function getStatistics(): array
    {
        return $this->requestRepository->getStatistics();
    }
}