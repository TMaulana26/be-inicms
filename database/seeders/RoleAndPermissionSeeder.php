<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define standard CMS permissions
        $permissions = [
            'view users' => 'User',
            'create users' => 'User',
            'edit users' => 'User',
            'delete users' => 'User',
            'view roles' => 'Role',
            'manage roles' => 'Role',
            'view permissions' => 'Permission',
            'manage permissions' => 'Permission',
            'view media' => 'Media',
            'upload media' => 'Media',
            'delete media' => 'Media',
        ];

        // 2. Create permissions
        foreach ($permissions as $name => $menu) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['menu' => $menu]
            );
        }

        // 3. Create roles and assign permissions

        // Super Admin (usually bypasses permission checks entirely via a Gate, but we give the role anyway)
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin (gets everything except maybe advanced super-admin specific stuff)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Editor (can handle media and standard content, but cannot manage users/roles)
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->givePermissionTo([
            'view media',
            'upload media',
            'delete media',
        ]);

        // Standard User (minimum/no permissions out of the box)
        $user = Role::firstOrCreate(['name' => 'user']);
    }
}
