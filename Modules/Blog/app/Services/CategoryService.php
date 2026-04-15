<?php

namespace Modules\Blog\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Str;
use Modules\Blog\Models\Category;

class CategoryService
{
    use HandlesIndexQuery;

    /**
     * Find a category by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): Category
    {
        $query = Category::query();
        if ($withTrashed) {
            $query->withTrashed();
        }
        return $query->findOrFail($id);
    }

    /**
     * Get list of categories with filters.
     */
    public function getCategories(array $params)
    {
        $query = Category::query()->withCount('posts');

        return $this->handleIndexQuery(
            $query,
            $params,
            ['name', 'slug', 'description'],
            fn($q) => $q->where('type', $params['type'] ?? 'post')
        );
    }

    /**
     * Create a new category.
     */
    public function createCategory(array $data)
    {
        $type = $data['type'] ?? 'post';
        $data['slug'] = $this->generateUniqueSlug($data['name'], null, $type);
        $data['type'] = $type;

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function updateCategory(Category $category, array $data)
    {
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $type = $data['type'] ?? $category->type;
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id, $type);
        }

        $category->update($data);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function deleteCategory(Category $category)
    {
        return $category->delete();
    }

    /**
     * Toggle a single category's status.
     */
    public function toggleStatus(Category $category): Category
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($category) {
            $category->update(['is_active' => !$category->is_active]);
            return $category->refresh();
        });
    }

    /**
     * Restore a category.
     */
    public function restore(string $id): Category
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $category = Category::onlyTrashed()->findOrFail($id);
            $category->restore();
            return $category->refresh();
        });
    }

    /**
     * Force delete a category.
     */
    public function forceDelete(string $id): Category
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $category = Category::onlyTrashed()->findOrFail($id);
            $categoryData = clone $category;
            $category->forceDelete();
            return $categoryData;
        });
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => Category::query(),
                'restore',
                'forceDelete' => Category::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $categories = $query->whereIn('id', $ids)->get();
            $foundIds = $categories->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($categories->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        Category::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        Category::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        Category::onlyTrashed()->whereIn('id', $foundIds)->forceDelete();
                        break;
                    case 'toggle':
                        /** @var Category $category */
                        foreach ($categories as $category) {
                            $category->update(['is_active' => !$category->is_active]);
                        }
                        break;
                }

                if ($operation !== 'forceDelete') {
                    // Refetch models to get their current state (especially for restore/toggle)
                    $categories = Category::withTrashed()->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $categories,
                'failed_ids' => $notFoundIds,
            ];
        });
    }

    /**
     * Generate a unique slug for the category.
     */
    protected function generateUniqueSlug(string $name, $ignoreId = null, string $type = 'post'): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Category::where('slug', $slug)
            ->where('type', $type)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
