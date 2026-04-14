<?php

namespace Modules\Menu\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use Modules\Menu\Http\Requests\Menu\IndexMenuRequest;
use Modules\Menu\Http\Requests\Menu\StoreMenuRequest;
use Modules\Menu\Http\Requests\Menu\UpdateMenuRequest;
use App\Traits\HandlesBulkAndSoftDeletes;
use Modules\Menu\Services\MenuService;
use Modules\Menu\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected MenuService $menuService
    ) {}

    protected function getService() { return $this->menuService; }
    protected function getResourceClass(): string { return MenuResource::class; }
    protected function getModelName(): string { return 'menu'; }
    protected function getEagerLoadRelations(): array { return ['children']; }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexMenuRequest $request): JsonResponse
    {
        $menus = $this->menuService->index($request->validated());

        return $this->paginatedResponse(
            MenuResource::collection($menus),
            'Menus retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request): JsonResponse
    {
        $menu = $this->menuService->store($request->validated());

        return $this->resourceResponse(
            new MenuResource($menu),
            'Menu created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu): JsonResponse
    {
        return $this->resourceResponse(
            new MenuResource($menu->load('children')),
            'Menu retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu): JsonResponse
    {
        $updatedMenu = $this->menuService->update($menu, $request->validated());

        return $this->resourceResponse(
            new MenuResource($updatedMenu),
            'Menu updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu): JsonResponse
    {
        $this->menuService->delete($menu);

        return $this->resourceResponse(
            new MenuResource($menu),
            'Menu deleted successfully.'
        );
    }

    /**
     * Toggle the status of the specified menu.
     */
    public function toggleStatus(Menu $menu): JsonResponse
    {
        $menu = $this->menuService->toggleStatus($menu);

        return $this->resourceResponse(
            new MenuResource($menu),
            'Menu status toggled successfully.'
        );
    }
}
