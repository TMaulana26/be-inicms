<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define standard CMS permissions with localized display names
        $permissions = [
            'view users' => [
                'menu' => 'User',
                'display_name' => ['en' => 'View Users', 'id' => 'Lihat Pengguna']
            ],
            'create users' => [
                'menu' => 'User',
                'display_name' => ['en' => 'Create Users', 'id' => 'Tambah Pengguna']
            ],
            'edit users' => [
                'menu' => 'User',
                'display_name' => ['en' => 'Edit Users', 'id' => 'Ubah Pengguna']
            ],
            'delete users' => [
                'menu' => 'User',
                'display_name' => ['en' => 'Delete Users', 'id' => 'Hapus Pengguna']
            ],
            'view roles' => [
                'menu' => 'Role',
                'display_name' => ['en' => 'View Roles', 'id' => 'Lihat Peran']
            ],
            'manage roles' => [
                'menu' => 'Role',
                'display_name' => ['en' => 'Manage Roles', 'id' => 'Kelola Peran']
            ],
            'view permissions' => [
                'menu' => 'Permission',
                'display_name' => ['en' => 'View Permissions', 'id' => 'Lihat Izin']
            ],
            'manage permissions' => [
                'menu' => 'Permission',
                'display_name' => ['en' => 'Manage Permissions', 'id' => 'Kelola Izin']
            ],
            'view media' => [
                'menu' => 'Media',
                'display_name' => ['en' => 'View Media', 'id' => 'Lihat Media']
            ],
            'upload media' => [
                'menu' => 'Media',
                'display_name' => ['en' => 'Upload Media', 'id' => 'Unggah Media']
            ],
            'delete media' => [
                'menu' => 'Media',
                'display_name' => ['en' => 'Delete Media', 'id' => 'Hapus Media']
            ],
        ];

        // 2. Create permissions
        foreach ($permissions as $name => $data) {
            Permission::updateOrCreate(
                ['name' => $name],
                [
                    'menu' => $data['menu'],
                    'display_name' => $data['display_name']
                ]
            );
        }

        // 3. Create roles and assign permissions
        $roles = [
            'super-admin' => [
                'en' => 'Super Admin',
                'id' => 'Administrator Utama'
            ],
            'admin' => [
                'en' => 'Administrator',
                'id' => 'Administrator'
            ],
            'editor' => [
                'en' => 'Editor',
                'id' => 'Editor'
            ],
            'user' => [
                'en' => 'Standard User',
                'id' => 'Pengguna Biasa'
            ],
        ];

        foreach ($roles as $name => $displayName) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['display_name' => $displayName]
            );

            if ($name === 'super-admin' || $name === 'admin') {
                $role->syncPermissions(Permission::all());
            } elseif ($name === 'editor') {
                $role->syncPermissions([
                    'view media',
                    'upload media',
                    'delete media',
                ]);
            }
        }
    }
}
