<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserService
{

    /**
     * Display a listing of the users resource.
     */
    public function index(array $params)
    {
        return User::query()
            ->when($params['trashed'] ?? null === 'only', fn($query) => $query->onlyTrashed())
            ->when($params['trashed'] ?? null === 'with', fn($query) => $query->withTrashed())
            ->when($params['status'] ?? null, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($params['search'] ?? null, function ($query, $search) {
                $query->where(fn($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->orderBy($params['sort_by'] ?? 'id', $params['sort_order'] ?? 'asc')
            ->paginate($params['per_page'] ?? 10)
            ->withQueryString();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user->refresh();
        });
    }

    /**
     * Update the specified user resource in storage.
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->update($data);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user->refresh();
        });
    }

    /**
     * Remove the specified user resource from storage (Soft Delete).
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    /**
     * Sync roles to a user (Replace existing roles).
     */
    public function syncRoles(User $user, array $roles): User
    {
        return DB::transaction(function () use ($user, $roles) {
            $user->syncRoles($roles);
            return $user->refresh();
        });
    }

    /**
     * Assign roles to a user (Additive).
     */
    public function assignRoles(User $user, array $roles): User
    {
        return DB::transaction(function () use ($user, $roles) {
            $user->assignRole($roles);
            return $user->refresh();
        });
    }

    /**
     * Remove roles from a user.
     */
    public function removeRoles(User $user, array $roles): User
    {
        return DB::transaction(function () use ($user, $roles) {
            foreach ($roles as $role) {
                $user->removeRole($role);
            }
            return $user->refresh();
        });
    }

    /**
     * Perform bulk operations on users.
     *
     * @param array $ids
     * @param string $operation (delete|restore|forceDelete|toggle)
     * @return array
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $query = match ($operation) {
                'delete',
                'toggle' => User::query(),
                'restore',
                'forceDelete' => User::onlyTrashed(),
                default => throw new \InvalidArgumentException("Invalid operation: {$operation}"),
            };

            $users = $query->whereIn('id', $ids)->get();
            $foundIds = $users->pluck('id')->toArray();
            $notFoundIds = array_values(array_diff($ids, $foundIds));

            if ($users->isNotEmpty()) {
                match ($operation) {
                    'delete' => User::whereIn('id', $foundIds)->delete(),
                    'restore' => User::onlyTrashed()->whereIn('id', $foundIds)->restore(),
                    'forceDelete' => User::onlyTrashed()->whereIn('id', $foundIds)->forceDelete(),
                    'toggle' => $users->each(fn($u) => $u->update(['is_active' => !$u->is_active])),
                };

                // Refresh models to reflect changes (e.g. deleted_at, is_active)
                if ($operation !== 'forceDelete') {
                    $users->each->refresh();
                }
            }

            return [
                'affected' => $users,
                'failed_ids' => $notFoundIds,
            ];
        });
    }

    /**
     * Toggle a single user's activity status.
     */
    public function toggleStatus(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->update(['is_active' => !$user->is_active]);
            return $user->refresh();
        });
    }

    /**
     * Restore a single user.
     */
    public function restore(string $id): User
    {
        return DB::transaction(function () use ($id) {
            $user = User::onlyTrashed()->findOrFail($id);
            $user->restore();
            return $user->refresh();
        });
    }

    /**
     * Force delete a single user.
     */
    public function forceDelete(string $id): User
    {
        return DB::transaction(function () use ($id) {
            $user = User::onlyTrashed()->findOrFail($id);
            // We clone or store data before deletion to return it in the resource if needed
            $userData = clone $user;
            $user->forceDelete();
            return $userData;
        });
    }
}
