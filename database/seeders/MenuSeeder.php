<?php

namespace Database\Seeders;

use App\Services\MenuService;
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
        $data = [
            'name' => 'Main Navigation',
            'slug' => 'main-navigation',
            'description' => 'The primary navigation for the website sidebar.',
            'items' => [
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
                ],
                [
                    'title' => 'Roles',
                    'icon' => 'ShieldCheck',
                    'url' => '/roles',
                    'order' => 2,
                ],
                [
                    'title' => 'Permissions',
                    'icon' => 'Key',
                    'url' => '/permissions',
                    'order' => 3,
                ],
                [
                    'title' => 'Media Library',
                    'icon' => 'ImageIcon',
                    'url' => '/media',
                    'order' => 4,
                ],
                [
                    'title' => 'Settings',
                    'icon' => 'Settings',
                    'url' => '/settings',
                    'order' => 5,
                ],
                [
                    'title' => 'My Profile',
                    'icon' => 'User',
                    'url' => '/profile',
                    'order' => 6,
                ],
            ],
        ];

        $menu = Menu::where('slug', $data['slug'])->first();

        if ($menu) {
            $this->menuService->update($menu, $data);
        } else {
            $this->menuService->store($data);
        }
    }
}
