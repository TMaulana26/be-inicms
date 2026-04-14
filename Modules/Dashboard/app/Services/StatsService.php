<?php

namespace Modules\Dashboard\Services;

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
            ],
            'categories' => [
                'all' => \Modules\Blog\Models\Category::count(),
                'active' => \Modules\Blog\Models\Category::where('is_active', true)->count(),
                'inactive' => \Modules\Blog\Models\Category::where('is_active', false)->count(),
                'deleted' => \Modules\Blog\Models\Category::onlyTrashed()->count(),
            ],
            'posts' => [
                'all' => \Modules\Blog\Models\Post::count(),
                'published' => \Modules\Blog\Models\Post::where('status', 'published')->count(),
                'draft' => \Modules\Blog\Models\Post::where('status', 'draft')->count(),
                'deleted' => \Modules\Blog\Models\Post::onlyTrashed()->count(),
            ],
            'pages' => [
                'all' => \Modules\Page\Models\Page::count(),
                'published' => \Modules\Page\Models\Page::where('status', 'published')->count(),
                'draft' => \Modules\Page\Models\Page::where('status', 'draft')->count(),
                'deleted' => \Modules\Page\Models\Page::onlyTrashed()->count(),
            ],
        ];
    }
}
