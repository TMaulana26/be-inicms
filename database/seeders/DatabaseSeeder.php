<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core / ACL (Dependencies)
        $this->call(RoleAndPermissionSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(SettingSeeder::class);

        // 2. Content Modules
        $this->call(PageSeeder::class);
    }
}
