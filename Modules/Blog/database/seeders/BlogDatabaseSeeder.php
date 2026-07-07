<?php

namespace Modules\Blog\Database\Seeders;

use Database\Seeders\CategorySeeder;
use Database\Seeders\PostSeeder;
use Illuminate\Database\Seeder;

class BlogDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            PostSeeder::class,
        ]);
    }
}
