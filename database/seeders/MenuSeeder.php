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
        // Clean up old menus to ensure a fresh structure
        Menu::withTrashed()->forceDelete();

        $menus = [
            [
                'title' => [
                    'en' => 'Home',
                    'id' => 'Beranda'
                ],
                'name' => [
                    'en' => 'Home',
                    'id' => 'Beranda'
                ],
                'icon' => 'Home',
                'url' => '/home',
                'order' => 0,
            ],
            [
                'title' => [
                    'en' => 'About Us',
                    'id' => 'Tentang Kami'
                ],
                'name' => [
                    'en' => 'About',
                    'id' => 'Tentang'
                ],
                'icon' => 'Info',
                'url' => '/about-us',
                'order' => 1,
            ],
            [
                'title' => [
                    'en' => 'Blog',
                    'id' => 'Blog'
                ],
                'name' => [
                    'en' => 'Blog',
                    'id' => 'Blog'
                ],
                'icon' => 'FileText',
                'url' => '/blog',
                'order' => 2,
                'children' => [
                    [
                        'title' => [
                            'en' => 'Categories',
                            'id' => 'Kategori'
                        ],
                        'url' => '/blog/categories',
                        'order' => 0,
                    ],
                    [
                        'title' => [
                            'en' => 'All Posts',
                            'id' => 'Semua Tulisan'
                        ],
                        'url' => '/blog/posts',
                        'order' => 1,
                    ],
                ]
            ],
            [
                'title' => [
                    'en' => 'Admin Panel',
                    'id' => 'Panel Admin'
                ],
                'name' => [
                    'en' => 'Admin',
                    'id' => 'Admin'
                ],
                'icon' => 'Settings',
                'url' => '/admin',
                'order' => 3,
                'children' => [
                    [
                        'title' => ['en' => 'Dashboard', 'id' => 'Dasbor'],
                        'url' => '/admin/dashboard',
                        'order' => 0,
                    ],
                    [
                        'title' => ['en' => 'Content', 'id' => 'Konten'],
                        'url' => '/admin/content',
                        'order' => 1,
                        'children' => [
                            ['title' => ['en' => 'Pages', 'id' => 'Halaman'], 'url' => '/admin/pages'],
                            ['title' => ['en' => 'Posts', 'id' => 'Postingan'], 'url' => '/admin/posts'],
                        ]
                    ],
                    [
                        'title' => ['en' => 'System', 'id' => 'Sistem'],
                        'url' => '/admin/system',
                        'order' => 2,
                        'children' => [
                            ['title' => ['en' => 'Users', 'id' => 'Pengguna'], 'url' => '/admin/users'],
                            ['title' => ['en' => 'Settings', 'id' => 'Pengaturan'], 'url' => '/admin/settings'],
                        ]
                    ],
                ]
            ],
        ];

        foreach ($menus as $menuData) {
            $this->menuService->store($menuData);
        }
    }
}
