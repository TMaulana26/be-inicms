<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Modules\Acl\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create the primary Super Admin based on user specifications
        $superAdmin = User::firstOrCreate(
            ['email' => 'inicmsadmin@yopmail.com'],
            [
                'name' => 'Super Administrator',
                'password' => env('ADMIN_PASSWORD', 'InICMS@2026_SecureAdmin!'),
                'email_verified_at' => Carbon::now(),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super-admin');
    }
}
