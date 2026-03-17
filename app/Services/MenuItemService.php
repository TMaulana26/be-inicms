<?php

namespace App\Services;

use Modules\Menu\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

use App\Traits\HandlesIndexQuery;

class MenuItemService
{
    use HandlesIndexQuery;

    /**
     * Find a menu item by its ID.
     */
    public function findById(string $id): MenuItem
    {
        return MenuItem::findOrFail($id);
    }

    /**
     * Display a listing of the menu items.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            MenuItem::query(),
            $params,
            ['title', 'url'],
            fn($q) => $q->when($params['menu_id'] ?? null, fn($subQ, $id) => $subQ->where('menu_id', $id))
                        ->when($params['parent_id'] ?? null, fn($subQ, $id) => $subQ->where('parent_id', $id)),
            10
        );
    }

    /**
     * Store a new menu item.
     */
    public function store(array $data): MenuItem
    {
        return DB::transaction(function () use ($data) {
            return MenuItem::create($data);
        });
    }

    /**
     * Update an existing menu item.
     */
    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        return DB::transaction(function () use ($menuItem, $data) {
            $menuItem->update($data);
            return $menuItem->refresh();
        });
    }

    /**
     * Delete a menu item.
     */
    public function delete(MenuItem $menuItem): bool
    {
        return $menuItem->delete();
    }

    /**
     * Toggle activity status.
     */
    public function toggleStatus(MenuItem $menuItem): MenuItem
    {
        return DB::transaction(function () use ($menuItem) {
            $menuItem->update(['is_active' => !$menuItem->is_active]);
            return $menuItem->refresh();
        });
    }

    /**
     * Restore a menu item.
     */
    public function restore(string $id): MenuItem
    {
        return DB::transaction(function () use ($id) {
            $menuItem = MenuItem::onlyTrashed()->findOrFail($id);
            $menuItem->restore();
            return $menuItem->refresh();
        });
    }

    /**
     * Force delete a menu item.
     */
    public function forceDelete(string $id): MenuItem
    {
        return DB::transaction(function () use ($id) {
            $menuItem = MenuItem::onlyTrashed()->findOrFail($id);
            $itemData = clone $menuItem;
            $menuItem->forceDelete();
            return $itemData;
        });
    }

    /**
     * Handle bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => MenuItem::query(),
                'restore',
                'forceDelete' => MenuItem::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $items = $query->whereIn('id', $ids)->get();
            $foundIds = $items->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($items->isNotEmpty()) {
                match ($operation) {
                    'delete' => MenuItem::whereIn('id', $foundIds)->delete(),
                    'restore' => MenuItem::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => MenuItem::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
                    'toggle' => $items->each(fn($i) => $i->update(['is_active' => !$i->is_active])),
                };

                if ($operation !== 'forceDelete') {
                    $items->each->refresh();
                }
            }

            return [
                'affected' => $items,
                'failed_ids' => $notFoundIds,
            ];
        });
    }
}
