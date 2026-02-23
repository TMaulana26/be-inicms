<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Http\Requests\Permission\StorePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Requests\Permission\IndexPermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\Shared\BulkRequest;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function __construct(
        protected \App\Services\PermissionService $permissionService
    ) {}

    /**
     * Display a listing of permissions.
     */
    public function index(IndexPermissionRequest $request): JsonResponse
    {
        $permissions = $this->permissionService->index($request->validated());

        if ($request->get('with_roles', false)) {
            $permissions->load('roles');
        }

        return $this->paginatedResponse(
            PermissionResource::collection($permissions),
            'Permissions retrieved successfully.'
        );
    }

    /**
     * Store a newly created permission.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionService->store($request->validated());

        return $this->resourceResponse(
            new PermissionResource($permission),
            'Permission created successfully.',
            201
        );
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission): JsonResponse
    {
        return $this->resourceResponse(
            new PermissionResource($permission->load('roles')),
            'Permission details retrieved successfully.'
        );
    }

    /**
     * Update the specified permission.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse
    {
        $permission = $this->permissionService->update($permission, $request->validated());

        return $this->resourceResponse(
            new PermissionResource($permission->load('roles')),
            'Permission updated successfully.'
        );
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Permission $permission): JsonResponse
    {
        $permission = $this->permissionService->toggleStatus($permission);

        return $this->resourceResponse(
            new PermissionResource($permission->load('roles')),
            'Permission status toggled successfully.'
        );
    }

    /**
     * Bulk toggle active status.
     */
    public function bulkToggleStatus(BulkRequest $request): JsonResponse
    {
        $result = $this->permissionService->handleBulkOperation($request->validated()['ids'], 'toggle');

        return $this->bulkResponse($result, 'status toggled', PermissionResource::class, 'permission', ['roles']);
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();

        return $this->resourceResponse(new PermissionResource($permission), 'Permission deleted successfully.');
    }
}
