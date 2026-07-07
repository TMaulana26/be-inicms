<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Models\User;
use Modules\Page\Models\Page;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        if (! $admin) {
            return;
        }

        $pages = [
            [
                'title' => [
                    'en' => 'Home',
                    'id' => 'Beranda',
                ],
                'slug' => 'home',
                'content' => [
                    'en' => '<h1>Welcome to INI CMS</h1><p>This is your localized home page. You can edit this content in the admin panel.</p>',
                    'id' => '<h1>Selamat Datang di INI CMS</h1><p>Ini adalah halaman beranda yang dilokalisasi. Anda dapat mengubah konten ini di panel admin.</p>',
                ],
                'status' => 'published',
            ],
            [
                'title' => [
                    'en' => 'About Us',
                    'id' => 'Tentang Kami',
                ],
                'slug' => 'about-us',
                'content' => [
                    'en' => '<h1>About Us</h1><p>We are a dedicated team providing the best CMS solutions for your business.</p>',
                    'id' => '<h1>Tentang Kami</h1><p>Kami adalah tim berdedikasi yang menyediakan solusi CMS terbaik untuk bisnis Anda.</p>',
                ],
                'status' => 'published',
            ],
            [
                'title' => [
                    'en' => 'Contact Us',
                    'id' => 'Hubungi Kami',
                ],
                'slug' => 'contact-us',
                'content' => [
                    'en' => '<h1>Contact Us</h1><p>Reach out to us at contact@example.com for any inquiries.</p>',
                    'id' => '<h1>Hubungi Kami</h1><p>Hubungi kami di contact@example.com untuk pertanyaan apa pun.</p>',
                ],
                'status' => 'published',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                array_merge($pageData, ['user_id' => $admin->id])
            );
        }
    }
}
