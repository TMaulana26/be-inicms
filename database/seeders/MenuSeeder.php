<?php

namespace Database\Seeders;

use Modules\Menu\Services\MenuService;
use Modules\Menu\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function __construct(
        protected MenuService $menuService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up old "Main Navigation" if it exists
        Menu::where('slug', 'main-navigation')->withTrashed()->forceDelete();

        $menus = [
            [
                'title' => 'Home',
                'icon' => 'Home',
                'url' => '/home',
                'order' => 0,
            ],
            [
                'title' => 'Users',
                'icon' => 'Users',
                'url' => '/users',
                'order' => 1,
                'children' => [
                    [
                        'title' => 'User Details',
                        'icon' => 'User',
                        'url' => '/users/:id',
                        'order' => 0,
                    ],
                ]
            ],
            [
                'title' => 'Roles',
                'icon' => 'ShieldCheck',
                'url' => '/roles',
                'order' => 2,
                'children' => [
                    [
                        'title' => 'Role Details',
                        'icon' => 'ShieldCheck',
                        'url' => '/roles/:id',
                        'order' => 0,
                    ],
                ]
            ],
            [
                'title' => 'Permissions',
                'icon' => 'Key',
                'url' => '/permissions',
                'order' => 3,
                'children' => [
                    [
                        'title' => 'Permission Details',
                        'icon' => 'Key',
                        'url' => '/permissions/:id',
                        'order' => 0,
                    ],
                ]
            ],
            [
                'title' => 'Media Library',
                'icon' => 'ImageIcon',
                'url' => '/media',
                'order' => 4,
            ],
            [
                'title' => 'Menus',
                'icon' => 'LayoutList',
                'url' => '/menus',
                'order' => 5,
                'children' => [
                    [
                        'title' => 'Menu Management',
                        'url' => '/menus',
                        'order' => 0,
                    ],
                    [
                        'title' => 'Menu Details',
                        'icon' => 'Menu',
                        'url' => '/menus/:id',
                        'order' => 1,
                    ],
                    [
                        'title' => 'Menu Items',
                        'icon' => 'LayoutList',
                        'url' => '/menus/items',
                        'order' => 2,
                    ],
                ]
            ],
            [
                'title' => 'Settings',
                'icon' => 'Settings',
                'url' => '/settings',
                'order' => 6,
            ],
            [
                'title' => 'My Profile',
                'icon' => 'User',
                'url' => '/profile',
                'order' => 7,
            ],
        ];

        foreach ($menus as $menuData) {
            // Match root menus by title
            $menu = Menu::where('title', $menuData['title'])->whereNull('parent_id')->first();
            
            if ($menu) {
                // Update existing root menu
                $this->menuService->update($menu, $menuData);
            } else {
                // Store new root menu
                $this->menuService->store($menuData);
            }
        }
    }
}
