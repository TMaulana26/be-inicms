<?php

namespace App\Services;

use Modules\Media\Models\Media;
use Modules\Acl\Models\User;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\Permission;

class StatsService
{
    /**
     * Get dashboard statistics for users, roles, and permissions.
     *
     * @return array
     */
    public function getDashboardStats(): array
    {
        return [
            'users' => [
                'all' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
                'deleted' => User::onlyTrashed()->count(),
            ],
            'roles' => [
                'all' => Role::count(),
                'active' => Role::where('is_active', true)->count(),
                'inactive' => Role::where('is_active', false)->count(),
                'deleted' => Role::onlyTrashed()->count(),
            ],
            'permissions' => [
                'all' => Permission::count(),
                'active' => Permission::where('is_active', true)->count(),
                'inactive' => Permission::where('is_active', false)->count(),
                'deleted' => Permission::onlyTrashed()->count(),
            ],
            'media' => [
                'all' => Media::count(),
                'active' => Media::where('is_active', true)->count(),
                'inactive' => Media::where('is_active', false)->count(),
                'deleted' => Media::onlyTrashed()->count(),
            ]
        ];
    }
}
