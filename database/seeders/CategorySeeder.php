<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Blog\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'type' => 'post',
                'name' => [
                    'en' => 'News',
                    'id' => 'Berita',
                ],
                'slug' => 'news',
                'description' => [
                    'en' => 'Latest project updates and news.',
                    'id' => 'Pembaruan proyek dan berita terbaru.',
                ],
                'is_active' => true,
            ],
            [
                'type' => 'post',
                'name' => [
                    'en' => 'Tutorials',
                    'id' => 'Tutorial',
                ],
                'slug' => 'tutorials',
                'description' => [
                    'en' => 'Helpful guides and tutorials.',
                    'id' => 'Panduan dan tutorial bermanfaat.',
                ],
                'is_active' => true,
            ],
            [
                'type' => 'post',
                'name' => [
                    'en' => 'General',
                    'id' => 'Umum',
                ],
                'slug' => 'general',
                'description' => [
                    'en' => 'General discussions and information.',
                    'id' => 'Diskusi dan informasi umum.',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['slug' => $catData['slug']],
                $catData
            );
        }
    }
}
