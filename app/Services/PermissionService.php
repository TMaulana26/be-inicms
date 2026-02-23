<?php

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Display a listing of permissions.
     */
    public function index(array $params)
    {
        return Permission::query()
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
     * Store a newly created permission.
     */
    public function store(array $data): Permission
    {
        return Permission::create($data);
    }

    /**
     * Update the specified permission.
     */
    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);
        return $permission->refresh();
    }

    /**
     * Handle bulk operations for permissions.
     */
    public function handleBulkOperation(array $ids, string $operation): array
    {
        return DB::transaction(function () use ($ids, $operation) {
            $foundIds = Permission::whereIn('id', $ids)->pluck('id')->toArray();
            $failedIds = array_values(array_diff($ids, $foundIds));

            $permissions = Permission::whereIn('id', $foundIds)->get();

            match ($operation) {
                'delete' => Permission::whereIn('id', $foundIds)->delete(),
                'toggle' => $permissions->each(fn($p) => $p->update(['is_active' => !$p->is_active])),
            };

            return [
                'affected' => Permission::whereIn('id', $foundIds)->get(),
                'failed_ids' => $failedIds
            ];
        });
    }

    /**
     * Toggle the active status of a permission.
     */
    public function toggleStatus(Permission $permission): Permission
    {
        $permission->update(['is_active' => !$permission->is_active]);
        return $permission;
    }
}
