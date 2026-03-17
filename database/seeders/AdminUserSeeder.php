<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

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
                'password' => '123456789', // Will be hashed automatically by Eloquent casts
                'email_verified_at' => Carbon::now(),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super-admin');

        // 2. Create 10 dummy users ranging from inicmsuser1@yopmail.com to inicmsuser10@yopmail.com
        for ($i = 1; $i <= 10; $i++) {
            $dummy = User::firstOrCreate(
                ['email' => "inicmsuser{$i}@yopmail.com"],
                [
                    'name' => "Dummy User {$i}",
                    'password' => '987654321',
                    'email_verified_at' => Carbon::now(),
                    'is_active' => true,
                ]
            );
            $dummy->assignRole('user');
        }
    }
}
