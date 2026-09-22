<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCommissionRateRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Exception;

class CategoryCommissionController extends Controller
{
    protected CategoryRepository $categoryRepository;
    protected CategoryService $categoryService;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryService $categoryService
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->categoryService = $categoryService;
    }

    // ===== UPDATE COMMISSION RATE =====

    public function update(UpdateCommissionRateRequest $request, Category $category)
    {
        try {
            $this->categoryService->updateCommissionRate(
                $category->id,
                $request->commission_rate,
                auth()->id(),
                $request->reason
            );

            return redirect()->back()
                ->with('success', 'Commission rate updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===== VIEW COMMISSION HISTORY =====

    public function history(Category $category)
    {
        $history = $this->categoryRepository->getCommissionHistory($category->id);

        return view('admin.categories.commission-history', compact('category', 'history'));
    }

    // ===== API - GET CURRENT RATE =====

    public function getCurrentRate(Category $category)
    {
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'commission_rate' => $category->commission_rate,
        ]);
    }
}