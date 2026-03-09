<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\AssignRoleRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Traits\HandlesBulkAndSoftDeletes;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected UserService $userService
    ) {}

    protected function getService()
    {
        return $this->userService;
    }

    protected function getResourceClass(): string
    {
        return UserResource::class;
    }

    protected function getModelName(): string
    {
        return 'user';
    }

    protected function getEagerLoadRelations(): array
    {
        return ['roles', 'permissions'];
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
