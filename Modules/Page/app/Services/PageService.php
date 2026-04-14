<?php

namespace Modules\Page\Services;

use App\Traits\HandlesIndexQuery;
use Illuminate\Support\Str;
use Modules\Page\Models\Page;
use Illuminate\Support\Facades\DB;

class PageService
{
    use HandlesIndexQuery;

    /**
     * Find a page by its ID.
     */
    public function findById(string $id, bool $withTrashed = false): Page
    {
        $query = Page::query();
        if ($withTrashed) {
            $query->withTrashed();
        }
        return $query->findOrFail($id);
    }

    /**
     * Get list of pages with filters.
     */
    public function getPages(array $params)
    {
        $query = Page::query()->with('author');

        return $this->handleIndexQuery(
            $query,
            $params,
            ['title', 'slug', 'content'],
            fn($q) => $q->when($params['status'] ?? null, fn($subQ, $status) => $subQ->where('status', $status))
        );
    }

    /**
     * Create a new page.
     */
    public function createPage(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
            $data['user_id'] = auth()->id();

            $page = Page::create($data);

            if (isset($data['page_image'])) {
                $page->addMedia($data['page_image'])
                    ->toMediaCollection('page_image');
            }

            return $page;
        });
    }

    /**
     * Update a page.
     */
    public function updatePage(Page $page, array $data)
    {
        return DB::transaction(function () use ($page, $data) {
            if (isset($data['title']) && $data['title'] !== $page->title) {
                $data['slug'] = $this->generateUniqueSlug($data['title'], $page->id);
            }

            $page->update($data);

            if (isset($data['page_image'])) {
                $page->clearMediaCollection('page_image');
                $page->addMedia($data['page_image'])
                    ->toMediaCollection('page_image');
            }

            return $page;
        });
    }

    /**
     * Delete a page.
     */
    public function deletePage(Page $page)
    {
        return $page->delete();
    }

    /**
     * Restore a page.
     */
    public function restore(string $id): Page
    {
        return DB::transaction(function () use ($id) {
            $page = Page::onlyTrashed()->findOrFail($id);
            $page->restore();
            return $page->refresh();
        });
    }

    /**
     * Force delete a page.
     */
    public function forceDelete(string $id): Page
    {
        return DB::transaction(function () use ($id) {
            $page = Page::onlyTrashed()->findOrFail($id);
            $pageData = clone $page;
            $page->forceDelete();
            return $pageData;
        });
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete' => Page::query(),
                'restore',
                'forceDelete' => Page::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $pages = $query->whereIn('id', $ids)->get();
            $foundIds = $pages->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($pages->isNotEmpty()) {
                switch ($operation) {
                    case 'delete':
                        Page::whereIn('id', $foundIds)->delete();
                        break;
                    case 'restore':
                        Page::onlyTrashed()->whereIn('id', $foundIds)->restore();
                        break;
                    case 'forceDelete':
                        Page::onlyTrashed()->whereIn('id', $foundIds)->forceDelete();
                        break;
                }

                if ($operation !== 'forceDelete') {
                    // Refetch models to get their current state (especially for restore)
                    $pages = Page::withTrashed()->with('author')->whereIn('id', $foundIds)->get();
                }
            }

            return [
                'affected' => $pages,
                'failed_ids' => $notFoundIds,
            ];
        });
    }

    /**
     * Generate a unique slug for the page.
     */
    protected function generateUniqueSlug(string $title, $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (Page::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }
}
