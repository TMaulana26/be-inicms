<?php

namespace Modules\Dashboard\Services;

use Modules\Acl\Models\Permission;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\User;

class StatsService
{
    /**
     * Get dashboard statistics for users, roles, and permissions.
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
                'all' => \Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->count(),
                'active' => \Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->where('is_active', true)->count(),
                'inactive' => \Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->where('is_active', false)->count(),
                'deleted' => \Illuminate\Support\Facades\DB::table('media')->whereNotNull('deleted_at')->count(),
            ],
            'pages' => [
                'all' => \Modules\Page\Models\Page::count(),
                'published' => \Modules\Page\Models\Page::where('status', 'published')->count(),
                'draft' => \Modules\Page\Models\Page::where('status', 'draft')->count(),
                'deleted' => \Modules\Page\Models\Page::onlyTrashed()->count(),
            ],
            'settings' => [
                'all' => \Modules\Setting\Models\Setting::count(),
                'active' => \Modules\Setting\Models\Setting::where('is_active', true)->count(),
                'inactive' => \Modules\Setting\Models\Setting::where('is_active', false)->count(),
                'deleted' => \Modules\Setting\Models\Setting::onlyTrashed()->count(),
            ],
        ];
    }
}
