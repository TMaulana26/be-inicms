<?php

use Modules\Acl\Models\User;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\Permission;
use Modules\Media\Models\Media;

test('guests cannot access stats endpoint', function () {
    $this->getJson('/api/v1/stats')->assertStatus(401);
});

test('authorized user can retrieve dashboard statistics', function () {
    $user = User::factory()->create(['is_active' => true]);

    // Create custom test records to verify counts
    // 1 additional active user (total users: 2, active: 2)
    User::factory()->create(['is_active' => true]);
    // 1 inactive user (total users: 3, active: 2, inactive: 1)
    User::factory()->create(['is_active' => false]);
    // 1 deleted user (total users: 3 + 1 soft-deleted, active: 2, inactive: 1, deleted: 1)
    $deletedUser = User::factory()->create();
    $deletedUser->delete();

    // Roles
    Role::create(['name' => 'editor', 'is_active' => true, 'display_name' => ['en' => 'Editor']]);
    Role::create(['name' => 'author', 'is_active' => false, 'display_name' => ['en' => 'Author']]);
    $deletedRole = Role::create(['name' => 'temp-role', 'is_active' => true, 'display_name' => ['en' => 'Temp']]);
    $deletedRole->delete();

    // Permissions
    Permission::create(['name' => 'edit-posts', 'is_active' => true, 'display_name' => ['en' => 'Edit Posts']]);
    Permission::create(['name' => 'delete-posts', 'is_active' => false, 'display_name' => ['en' => 'Delete Posts']]);
    $deletedPermission = Permission::create(['name' => 'temp-permission', 'is_active' => true, 'display_name' => ['en' => 'Temp']]);
    $deletedPermission->delete();

    // Media
    Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image',
        'file_name' => 'avatar.png',
        'disk' => 'public',
        'size' => 500,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image-inactive',
        'file_name' => 'avatar2.png',
        'disk' => 'public',
        'size' => 600,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => false,
    ]);
    $deletedMedia = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image-deleted',
        'file_name' => 'avatar3.png',
        'disk' => 'public',
        'size' => 700,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    $deletedMedia->delete();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/stats')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'users' => ['all', 'active', 'inactive', 'deleted'],
                'roles' => ['all', 'active', 'inactive', 'deleted'],
                'permissions' => ['all', 'active', 'inactive', 'deleted'],
                'media' => ['all', 'active', 'inactive', 'deleted'],
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => [
                'users' => [
                    'all' => 3,
                    'active' => 2,
                    'inactive' => 1,
                    'deleted' => 1,
                ],
                'roles' => [
                    'all' => 2,
                    'active' => 1,
                    'inactive' => 1,
                    'deleted' => 1,
                ],
                'permissions' => [
                    'all' => 2,
                    'active' => 1,
                    'inactive' => 1,
                    'deleted' => 1,
                ],
                'media' => [
                    'all' => 2,
                    'active' => 1,
                    'inactive' => 1,
                    'deleted' => 1,
                ],
            ],
        ]);
});
