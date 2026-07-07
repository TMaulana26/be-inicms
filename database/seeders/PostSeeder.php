<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Models\User;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $newsCategory = Category::where('slug', 'news')->first();
        if (! $admin || ! $newsCategory) {
            return;
        }

        $posts = [
            [
                'category_id' => $newsCategory->id,
                'title' => [
                    'en' => 'Welcome to our Headless CMS',
                    'id' => 'Selamat Datang di Headless CMS kami',
                ],
                'slug' => 'welcome-to-headless-cms',
                'summary' => [
                    'en' => 'Discover the power of API-first content delivery.',
                    'id' => 'Temukan kekuatan pengiriman konten berbasis API.',
                ],
                'content' => [
                    'en' => '<p>This is the first post in your new CMS. It supports multiple languages and is fully manageable via API.</p>',
                    'id' => '<p>Ini adalah postingan pertama di CMS baru Anda. Ini mendukung banyak bahasa dan sepenuhnya dapat dikelola melalui API.</p>',
                ],
                'status' => 'published',
                'is_featured' => true,
                'published_at' => now(),
            ],
            [
                'category_id' => $newsCategory->id,
                'title' => [
                    'en' => 'Getting Started with be-inicms',
                    'id' => 'Memulai dengan be-inicms',
                ],
                'slug' => 'getting-started-with-be-inicms',
                'summary' => [
                    'en' => 'Learn how to set up and manage your new CMS.',
                    'id' => 'Pelajari cara mengatur dan mengelola CMS baru Anda.',
                ],
                'content' => [
                    'en' => '<p>be-inicms is built with Laravel and follows modular development practices.</p>',
                    'id' => '<p>be-inicms dibangun dengan Laravel dan mengikuti praktik pengembangan modular.</p>',
                ],
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
            ],
        ];

        foreach ($posts as $postData) {
            Post::updateOrCreate(
                ['slug' => $postData['slug']],
                array_merge($postData, ['user_id' => $admin->id])
            );
        }
    }
}
