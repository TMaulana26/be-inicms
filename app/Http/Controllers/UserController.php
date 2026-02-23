<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\Shared\BulkRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

use App\Http\Requests\User\AssignRoleRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * Display a listing of the users resource.
     */
    public function index(IndexUserRequest $request): JsonResponse
    {
        $users = $this->userService->index($request->validated());

        $users->load('roles', 'permissions');

        return $this->paginatedResponse(
            UserResource::collection($users),
            'Users retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());

        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'User created successfully.',
            201
        );
    }

    /**
     * Display the specified user resource.
     */
    public function show(User $user): JsonResponse
    {
        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'User details retrieved successfully.'
        );
    }

    /**
     * Update the specified user resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'User updated successfully.'
        );
    }

    /**
     * Remove the specified user resource from storage (Soft Delete).
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->resourceResponse(new UserResource($user), 'User deleted successfully.');
    }

    /**
     * Restore the specified soft-deleted user resource.
     */
    public function restore(string $id): JsonResponse
    {
        $user = $this->userService->restore($id);

        return $this->resourceResponse(
            new UserResource($user),
            'User restored successfully.'
        );
    }

    /**
     * Toggle the active status of the specified user.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $user = $this->userService->toggleStatus($user);

        return $this->resourceResponse(
            new UserResource($user),
            'User status toggled successfully.'
        );
    }

    /**
     * Permanently remove the specified user resource from storage.
     */
    public function forceDelete(string $id): JsonResponse
    {
        $user = $this->userService->forceDelete($id);

        return $this->resourceResponse(
            new UserResource($user),
            'User permanently deleted.'
        );
    }

    /**
     * Remove multiple users from storage (Soft Delete).
     */
    public function bulkDestroy(BulkRequest $request): JsonResponse
    {
        $result = $this->userService->handleBulkOperation($request->validated()['ids'], 'delete');

        return $this->bulkResponse($result, 'deleted', UserResource::class, 'user', ['roles', 'permissions']);
    }

    /**
     * Toggle active status for multiple users.
     */
    public function bulkToggleStatus(BulkRequest $request): JsonResponse
    {
        $result = $this->userService->handleBulkOperation($request->validated()['ids'], 'toggle');

        return $this->bulkResponse($result, 'status toggled', UserResource::class, 'user', ['roles', 'permissions']);
    }

    /**
     * Restore multiple soft-deleted users.
     */
    public function bulkRestore(BulkRequest $request): JsonResponse
    {
        $result = $this->userService->handleBulkOperation($request->validated()['ids'], 'restore');

        return $this->bulkResponse($result, 'restored', UserResource::class, 'user', ['roles', 'permissions']);
    }

    /**
     * Permanently remove multiple users from storage.
     */
    public function bulkForceDelete(BulkRequest $request): JsonResponse
    {
        $result = $this->userService->handleBulkOperation($request->validated()['ids'], 'forceDelete');

        return $this->bulkResponse($result, 'permanently deleted', UserResource::class, 'user', ['roles', 'permissions']);
    }

    /**
     * Sync roles to the specified user (Replace existing).
     */
    public function syncRoles(AssignRoleRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->syncRoles($user, $request->validated()['roles']);

        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'Roles synced successfully.'
        );
    }

    /**
     * Assign roles to the specified user (Additive).
     */
    public function assignRoles(AssignRoleRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->assignRoles($user, $request->validated()['roles']);

        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'Roles assigned successfully.'
        );
    }

    /**
     * Remove roles from the specified user.
     */
    public function removeRoles(AssignRoleRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->removeRoles($user, $request->validated()['roles']);

        return $this->resourceResponse(
            new UserResource($user->load('roles', 'permissions')),
            'Roles removed successfully.'
        );
    }

}
