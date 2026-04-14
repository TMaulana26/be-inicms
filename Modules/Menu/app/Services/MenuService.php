<?php

namespace Modules\Menu\Services;

use Modules\Menu\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Traits\HandlesIndexQuery;

class MenuService
{
    use HandlesIndexQuery;

    /**
     * Find a menu by its ID or slug.
     */
    public function findByIdOrSlug(string $id): Menu
    {
        return Menu::where('id', $id)->orWhere('slug', $id)->firstOrFail();
    }

    /**
     * Display a listing of the menus.
     */
    public function index(array $params)
    {
        $query = Menu::query();

        // If no parent_id is specified in filters, default to root menus
        if (!isset($params['parent_id']) && !isset($params['all'])) {
            $query->roots();
        }

        return $this->handleIndexQuery(
            $query,
            $params,
            ['name', 'slug', 'title'],
            fn($q) => $q->with(['children'])
        );
    }

    /**
     * Store a new menu.
     */
    public function store(array $data): Menu
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['title']) && !isset($data['name'])) {
                $data['name'] = $data['title'];
            }
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            $menu = Menu::create(Arr::except($data, ['children']));

            if (isset($data['children'])) {
                $this->syncChildren($menu, $data['children']);
            }

            return $menu->load('children');
        });
    }

    /**
     * Update an existing menu.
     */
    public function update(Menu $menu, array $data): Menu
    {
        return DB::transaction(function () use ($menu, $data) {
            if (isset($data['title']) && !isset($data['name'])) {
                $data['name'] = $data['title'];
            }
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            $menu->update(Arr::except($data, ['children']));

            if (isset($data['children'])) {
                // Delete existing children and rebuild
                $menu->children()->delete();
                $this->syncChildren($menu, $data['children']);
            }

            return $menu->load('children');
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
     * Toggle status.
     */
    public function toggleStatus(Menu $menu): Menu
    {
        $menu->update(['is_active' => !$menu->is_active]);
        return $menu->refresh();
    }

    /**
     * Restore a menu.
     */
    public function restore(string $id): Menu
    {
        $menu = Menu::onlyTrashed()->findOrFail($id);
        $menu->restore();
        return $menu->refresh();
    }

    /**
     * Force delete.
     */
    public function forceDelete(string $id): Menu
    {
        $menu = Menu::onlyTrashed()->findOrFail($id);
        $menuData = clone $menu;
        $menu->forceDelete();
        return $menuData;
    }

    /**
     * Synchronize children recursively.
     */
    protected function syncChildren(Menu $parent, array $children): void
    {
        foreach ($children as $index => $childData) {
            $child = Menu::create(array_merge($childData, [
                'parent_id' => $parent->id,
                'order' => $childData['order'] ?? $index,
            ]));

            if (isset($childData['children']) && is_array($childData['children'])) {
                $this->syncChildren($child, $childData['children']);
            }
        }
    }

    /**
     * Perform bulk operations.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete', 'toggle' => Menu::query(),
                'restore', 'forceDelete' => Menu::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $items = $query->whereIn('id', $ids)->get();
            $foundIds = $items->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($items->isNotEmpty()) {
                match ($operation) {
                    'delete' => Menu::whereIn('id', $foundIds)->delete(),
                    'restore' => Menu::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => Menu::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
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
