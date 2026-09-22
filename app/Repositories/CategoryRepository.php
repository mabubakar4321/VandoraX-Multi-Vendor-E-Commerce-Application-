<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\CategoryCommissionHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function getHierarchy(): Collection
    {
        return $this->model
            ->with('children.children')
            ->main()
            ->ordered()
            ->get();
    }

    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    public function getActiveMain(): Collection
    {
        return $this->model
            ->active()
            ->main()
            ->ordered()
            ->get();
    }

    public function getActiveSubcategoriesFor(int $parentId): Collection
    {
        return $this->model
            ->active()
            ->where('parent_id', $parentId)
            ->ordered()
            ->get();
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->where('name', 'like', "%{$query}%")
            ->orWhere('slug', 'like', "%{$query}%")
            ->ordered()
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->with(['creator', 'children'])
            ->main()
            ->ordered()
            ->paginate($perPage);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        return $this->model
            ->where('name', $name)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->exists();
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        return Category::slugExists($slug, $excludeId);
    }

    public function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        return Category::generateSlug($name, $excludeId);
    }

    public function getCommissionRate(int $categoryId): float
    {
        return $this->model->find($categoryId)?->commission_rate ?? 10.0;
    }

    public function updateCommissionRate(
        int $categoryId,
        float $newRate,
        int $changedBy,
        ?string $reason = null
    ): bool {
        $category = $this->model->find($categoryId);
        if (!$category) {
            return false;
        }

        CategoryCommissionHistory::create([
            'category_id' => $categoryId,
            'old_commission' => $category->commission_rate,
            'new_commission' => $newRate,
            'changed_by' => $changedBy,
            'reason' => $reason,
        ]);

        return $category->update(['commission_rate' => $newRate]);
    }

    public function getCommissionHistory(int $categoryId): Collection
    {
        return CategoryCommissionHistory::where('category_id', $categoryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deactivate(int $categoryId): bool
    {
        return $this->model->find($categoryId)->update(['is_active' => false]);
    }

    public function activate(int $categoryId): bool
    {
        return $this->model->find($categoryId)->update(['is_active' => true]);
    }

    public function canDelete(int $categoryId): bool
    {
        $category = $this->model->find($categoryId);
        return $category ? $category->canBeDeleted() : false;
    }

    public function getDeletionReasons(int $categoryId): array
    {
        $category = $this->model->find($categoryId);
        return $category ? $category->getDeletionReasons() : [];
    }

    public function getCategoryTree(): array
    {
        $categories = $this->model->main()->ordered()->get();
        
        return $categories->map(fn ($category) => [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'commission_rate' => $category->commission_rate,
            'is_active' => $category->is_active,
            'product_count' => $category->products()->count(),
            'subcategories' => $this->getSubcategoryTree($category->id),
        ])->toArray();
    }

    private function getSubcategoryTree(int $parentId): array
    {
        $subcategories = $this->model->where('parent_id', $parentId)->ordered()->get();
        
        return $subcategories->map(fn ($subcategory) => [
            'id' => $subcategory->id,
            'name' => $subcategory->name,
            'slug' => $subcategory->slug,
            'commission_rate' => $subcategory->commission_rate,
            'is_active' => $subcategory->is_active,
            'product_count' => $subcategory->products()->count(),
            'parent_id' => $subcategory->parent_id,
        ])->toArray();
    }
}