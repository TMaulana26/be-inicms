<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuItemResource;
use App\Http\Requests\MenuItem\IndexMenuItemRequest;
use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use App\Traits\HandlesBulkAndSoftDeletes;
use App\Services\MenuItemService;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;

class MenuItemController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected MenuItemService $menuItemService
    ) {}

    protected function getService() { return $this->menuItemService; }
    protected function getResourceClass(): string { return MenuItemResource::class; }
    protected function getModelName(): string { return 'menu item'; }
    protected function getEagerLoadRelations(): array { return ['children']; }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexMenuItemRequest $request): JsonResponse
    {
        $items = $this->menuItemService->index($request->validated());

        return $this->paginatedResponse(
            MenuItemResource::collection($items),
            'Menu items retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuItemRequest $request): JsonResponse
    {
        $item = $this->menuItemService->store($request->validated());

        return $this->resourceResponse(
            new MenuItemResource($item),
            'Menu item created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem): JsonResponse
    {
        return $this->resourceResponse(
            new MenuItemResource($menuItem->load('children')),
            'Menu item retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): JsonResponse
    {
        $updatedItem = $this->menuItemService->update($menuItem, $request->validated());

        return $this->resourceResponse(
            new MenuItemResource($updatedItem),
            'Menu item updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $this->menuItemService->delete($menuItem);

        return $this->resourceResponse(
            new MenuItemResource($menuItem),
            'Menu item deleted successfully.'
        );
    }

    /**
     * Toggle status.
     */
    public function toggleStatus(MenuItem $menuItem): JsonResponse
    {
        $item = $this->menuItemService->toggleStatus($menuItem);

        return $this->resourceResponse(
            new MenuItemResource($item),
            'Menu item status toggled successfully.'
        );
    }
}
