<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Requests\Role\IndexRoleRequest;
use App\Http\Requests\Shared\BulkRequest;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected \App\Services\RoleService $roleService
    ) {}

    /**
     * Display a listing of roles.
     */
    public function index(IndexRoleRequest $request): JsonResponse
    {
        $roles = $this->roleService->index($request->validated());

        if ($request->get('with_permissions', false)) {
            $roles->load('permissions');
        }

        return $this->paginatedResponse(
            RoleResource::collection($roles),
            'Roles retrieved successfully.'
        );
    }

    /**
     * Store a newly created role.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->store($request->validated());

        return $this->resourceResponse(
            new RoleResource($role->load('permissions')),
            'Role created successfully.',
            201
        );
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        return $this->resourceResponse(
            new RoleResource($role->load('permissions')),
            'Role details retrieved successfully.'
        );
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update($role, $request->validated());

        return $this->resourceResponse(
            new RoleResource($role->load('permissions')),
            'Role updated successfully.'
        );
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(Role $role): JsonResponse
    {
        $role = $this->roleService->toggleStatus($role);

        return $this->resourceResponse(
            new RoleResource($role->load('permissions')),
            'Role status toggled successfully.'
        );
    }

    /**
     * Bulk toggle active status.
     */
    public function bulkToggleStatus(BulkRequest $request): JsonResponse
    {
        $result = $this->roleService->handleBulkOperation($request->validated()['ids'], 'toggle');

        return $this->bulkResponse($result, 'status toggled', RoleResource::class, 'role', ['permissions']);
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return $this->resourceResponse(new RoleResource($role), 'Role deleted successfully.');
    }
}
