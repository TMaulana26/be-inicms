<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService
{
    /**
     * Display a listing of roles.
     */
    public function index(array $params)
    {
        return Role::query()
            ->when($params['status'] ?? null, function ($query, $status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($params['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%");
            })
            ->orderBy($params['sort_by'] ?? 'id', $params['sort_order'] ?? 'asc')
            ->paginate($params['per_page'] ?? 10)
            ->withQueryString();
    }

    /**
     * Store a newly created role.
     */
    public function store(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name']]);

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->refresh();
        });
    }

    /**
     * Update the specified role.
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            if (isset($data['name'])) {
                $role->name = $data['name'];
                $role->save();
            }

            if (isset($data['permissions'])) {
                $role->syncPermissions($data['permissions']);
            }

            return $role->refresh();
        });
    }

    /**
     * Handle bulk operations for roles.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $foundIds = Role::whereIn('id', $ids)->pluck('id')->toArray();
            $failedIds = array_values(array_diff($ids, $foundIds));

            $roles = Role::whereIn('id', $foundIds)->get();

            match ($operation) {
                'delete' => Role::whereIn('id', $foundIds)->delete(),
                'toggle' => $roles->each(fn($r) => $r->update(['is_active' => !$r->is_active])),
            };

            return [
                'affected' => Role::whereIn('id', $foundIds)->get(),
                'failed_ids' => $failedIds
            ];
        });
    }

    /**
     * Toggle the active status of a role.
     */
    public function toggleStatus(Role $role): Role
    {
        $role->update(['is_active' => !$role->is_active]);
        return $role;
    }
}
