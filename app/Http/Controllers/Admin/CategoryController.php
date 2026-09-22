<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

    // ===== LIST CATEGORIES =====

    public function index(Request $request)
    {
        $query = $request->get('q');
        
        if ($query) {
            $categories = $this->categoryRepository->search($query);
        } else {
            $categories = $this->categoryRepository->getPaginated(15);
        }

        $statistics = [
            'total' => Category::count(),
            'active' => Category::active()->count(),
            'main' => Category::main()->count(),
            'sub' => Category::sub()->count(),
        ];

        return view('admin.categories.index', compact('categories', 'statistics', 'query'));
    }

    // ===== CREATE CATEGORY =====

    public function create()
    {
        $mainCategories = $this->categoryRepository->getActiveMain();
        return view('admin.categories.create', compact('mainCategories'));
    }

    public function store(CreateCategoryRequest $request)
    {
        try {
            $this->categoryService->createCategory(
                $request->validated(),
                auth()->id()
            );

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===== EDIT CATEGORY =====

    public function edit(Category $category)
    {
        $mainCategories = $this->categoryRepository->getActiveMain()
            ->where('id', '!=', $category->id);

        $commissionHistory = $this->categoryRepository->getCommissionHistory($category->id);

        return view('admin.categories.edit', compact('category', 'mainCategories', 'commissionHistory'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $this->categoryService->updateCategory(
                $category->id,
                $request->validated(),
                auth()->id()
            );

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ===== DELETE CATEGORY =====

    public function destroy(Category $category)
    {
        try {
            // Check if can be deleted
            if (!$category->canBeDeleted()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete: ' . implode(', ', $category->getDeletionReasons()));
            }

            $this->categoryService->deleteCategory($category->id);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ===== DEACTIVATE CATEGORY =====

    public function deactivate(Category $category)
    {
        try {
            $this->categoryService->deactivateCategory($category->id);

            return redirect()->back()
                ->with('success', 'Category deactivated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ===== ACTIVATE CATEGORY =====

    public function activate(Category $category)
    {
        try {
            $this->categoryService->activateCategory($category->id);

            return redirect()->back()
                ->with('success', 'Category activated successfully!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ===== TREE VIEW =====

    public function tree()
    {
        $tree = $this->categoryService->getCategoryHierarchy();

        return view('admin.categories.tree', compact('tree'));
    }

    // ===== API - GET SUBCATEGORIES =====

    public function getSubcategories(Category $category)
    {
        $subcategories = $this->categoryRepository->getActiveSubcategoriesFor($category->id);

        return response()->json([
            'data' => $subcategories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'commission_rate' => $cat->commission_rate,
            ])->values(),
        ]);
    }
}