<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function createCategory(array $data, int $userId): Category
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['name']);
            $data['created_by'] = $userId;

            if (isset($data['icon']) && $data['icon']) {
                $data['icon'] = $this->handleIconUpload($data['icon']);
            }

            return $this->categoryRepository->create($data);
        });
    }

    public function updateCategory(int $categoryId, array $data, int $userId): Category
    {
        return DB::transaction(function () use ($categoryId, $data, $userId) {
            $category = $this->categoryRepository->findOrFail($categoryId);

            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = $this->categoryRepository->generateUniqueSlug($data['name'], $categoryId);
            }

            if (isset($data['icon']) && $data['icon']) {
                if ($category->icon) {
                    Storage::disk('public')->delete($category->icon);
                }
                $data['icon'] = $this->handleIconUpload($data['icon']);
            }

            $category->update($data);
            return $category;
        });
    }

    public function deleteCategory(int $categoryId): bool
    {
        return DB::transaction(function () use ($categoryId) {
            $category = $this->categoryRepository->findOrFail($categoryId);

            if (!$category->canBeDeleted()) {
                throw new Exception(
                    'Cannot delete category: ' . implode(', ', $category->getDeletionReasons())
                );
            }

            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            return $category->forceDelete();
        });
    }

    public function deactivateCategory(int $categoryId): bool
    {
        return $this->categoryRepository->deactivate($categoryId);
    }

    public function activateCategory(int $categoryId): bool
    {
        return $this->categoryRepository->activate($categoryId);
    }

    public function updateCommissionRate(
        int $categoryId,
        float $newRate,
        int $userId,
        ?string $reason = null
    ): bool {
        return $this->categoryRepository->updateCommissionRate(
            $categoryId,
            $newRate,
            $userId,
            $reason
        );
    }

    protected function handleIconUpload($file): string
    {
        $path = $file->store('categories/icons', 'public');
        return $path;
    }

    public function getCategoryHierarchy()
    {
        return $this->categoryRepository->getCategoryTree();
    }

    public function validateDeletion(int $categoryId): array
    {
        return [
            'can_delete' => $this->categoryRepository->canDelete($categoryId),
            'reasons' => $this->categoryRepository->getDeletionReasons($categoryId),
        ];
    }
}