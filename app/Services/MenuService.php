<?php

namespace App\Services;

use Modules\Menu\Models\Menu;
use Modules\Menu\Models\MenuItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use App\Traits\HandlesIndexQuery;

class MenuService
{
    use HandlesIndexQuery;

    /**
     * Find a menu by its ID.
     */
    public function findById(string $id): Menu
    {
        return Menu::findOrFail($id);
    }

    /**
     * Display a listing of the menus.
     */
    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Menu::query(),
            $params,
            ['name', 'slug'],
            fn($q) => $q->with(['items.children'])
        );
    }

    /**
     * Store a new menu.
     */
    public function store(array $data): Menu
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            $menu = Menu::create(Arr::except($data, ['items']));

            if (isset($data['items'])) {
                $this->syncItems($menu, $data['items']);
            }

            return $menu->load('items.children');
        });
    }

    /**
     * Update an existing menu.
     */
    public function update(Menu $menu, array $data): Menu
    {
        return DB::transaction(function () use ($menu, $data) {
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $menu->update(Arr::except($data, ['items']));

            if (isset($data['items'])) {
                // Delete existing items and rebuild to simplify sync
                $menu->allItems()->delete();
                $this->syncItems($menu, $data['items']);
            }

            return $menu->load('items.children');
        });
    }

    /**
     * Delete a menu.
     */
    public function delete(Menu $menu): bool
    {
        return $menu->delete();
    }

    /**
     * Toggle a single menu's activity status.
     */
    public function toggleStatus(Menu $menu): Menu
    {
        return DB::transaction(function () use ($menu) {
            $menu->update(['is_active' => !$menu->is_active]);
            return $menu->refresh();
        });
    }

    /**
     * Restore a single menu.
     */
    public function restore(string $id): Menu
    {
        return DB::transaction(function () use ($id) {
            $menu = Menu::onlyTrashed()->findOrFail($id);
            $menu->restore();
            return $menu->refresh();
        });
    }

    /**
     * Force delete a single menu.
     */
    public function forceDelete(string $id): Menu
    {
        return DB::transaction(function () use ($id) {
            $menu = Menu::onlyTrashed()->findOrFail($id);
            $menuData = clone $menu;
            $menu->forceDelete();
            return $menuData;
        });
    }

    /**
     * Perform bulk operations on menus.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => Menu::query(),
                'restore',
                'forceDelete' => Menu::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $menus = $query->whereIn('id', $ids)->get();
            $foundIds = $menus->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($menus->isNotEmpty()) {
                match ($operation) {
                    'delete' => Menu::whereIn('id', $foundIds)->delete(),
                    'restore' => Menu::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => Menu::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
                    'toggle' => $menus->each(fn($u) => $u->update(['is_active' => !$u->is_active])),
                };

                if ($operation !== 'forceDelete') {
                    $menus->each->refresh();
                }
            }

            return [
                'affected' => $menus,
                'failed_ids' => $notFoundIds,
            ];
        });
    }

    /**
     * Synchronize menu items recursively.
     */
    protected function syncItems(Menu $menu, array $items, $parentId = null): void
    {
        foreach ($items as $index => $itemData) {
            $item = MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $parentId,
                'title' => $itemData['title'],
                'icon' => $itemData['icon'] ?? null,
                'is_active' => $itemData['is_active'] ?? true,
                'url' => $itemData['url'] ?? null,
                'target' => $itemData['target'] ?? '_self',
                'order' => $itemData['order'] ?? $index,
            ]);

            if (isset($itemData['children']) && is_array($itemData['children'])) {
                $this->syncItems($menu, $itemData['children'], $item->id);
            }
        }
    }
}
