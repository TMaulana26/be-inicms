<?php

use Modules\Acl\Models\Permission;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\User;
use Modules\Page\Models\Page;
use Modules\Setting\Models\Setting;

test('guests cannot access stats endpoint', function () {
    $this->getJson('/api/v1/stats')->assertStatus(401);
});

test('authorized user can retrieve dashboard statistics', function () {
    $user = User::factory()->create(['is_active' => true]);

    // Create custom test records to verify counts
    // 1 additional active user
    User::factory()->create(['is_active' => true]);
    // 1 inactive user
    User::factory()->create(['is_active' => false]);
    // 1 deleted user
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
    Illuminate\Support\Facades\DB::table('media')->insert([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image',
        'file_name' => 'avatar.png',
        'disk' => 'public',
        'size' => 500,
        'manipulations' => json_encode([]),
        'custom_properties' => json_encode([]),
        'generated_conversions' => json_encode([]),
        'responsive_images' => json_encode([]),
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Illuminate\Support\Facades\DB::table('media')->insert([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image-inactive',
        'file_name' => 'avatar2.png',
        'disk' => 'public',
        'size' => 600,
        'manipulations' => json_encode([]),
        'custom_properties' => json_encode([]),
        'generated_conversions' => json_encode([]),
        'responsive_images' => json_encode([]),
        'is_active' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Illuminate\Support\Facades\DB::table('media')->insert([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'avatar',
        'name' => 'avatar-image-deleted',
        'file_name' => 'avatar3.png',
        'disk' => 'public',
        'size' => 700,
        'manipulations' => json_encode([]),
        'custom_properties' => json_encode([]),
        'generated_conversions' => json_encode([]),
        'responsive_images' => json_encode([]),
        'is_active' => true,
        'deleted_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Pages
    Page::factory()->create(['status' => 'published']);
    Page::factory()->create(['status' => 'draft']);
    $deletedPage = Page::factory()->create();
    $deletedPage->delete();

    // Settings
    Setting::create(['key' => 'set_1', 'name' => 'Set 1', 'value' => 'val1', 'type' => 'text', 'group' => 'general', 'is_active' => true]);
    Setting::create(['key' => 'set_2', 'name' => 'Set 2', 'value' => 'val2', 'type' => 'text', 'group' => 'general', 'is_active' => false]);
    $deletedSetting = Setting::create(['key' => 'set_3', 'name' => 'Set 3', 'value' => 'val3', 'type' => 'text', 'group' => 'general', 'is_active' => true]);
    $deletedSetting->delete();

    $expectedStats = [
        'users' => [
            'all' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'deleted' => User::onlyTrashed()->count(),
        ],
        'roles' => [
            'all' => Role::count(),
            'active' => Role::where('is_active', true)->count(),
            'inactive' => Role::where('is_active', false)->count(),
            'deleted' => Role::onlyTrashed()->count(),
        ],
        'permissions' => [
            'all' => Permission::count(),
            'active' => Permission::where('is_active', true)->count(),
            'inactive' => Permission::where('is_active', false)->count(),
            'deleted' => Permission::onlyTrashed()->count(),
        ],
        'media' => [
            'all' => Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->count(),
            'active' => Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->where('is_active', true)->count(),
            'inactive' => Illuminate\Support\Facades\DB::table('media')->whereNull('deleted_at')->where('is_active', false)->count(),
            'deleted' => Illuminate\Support\Facades\DB::table('media')->whereNotNull('deleted_at')->count(),
        ],
        'pages' => [
            'all' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
            'deleted' => Page::onlyTrashed()->count(),
        ],
        'settings' => [
            'all' => Setting::count(),
            'active' => Setting::where('is_active', true)->count(),
            'inactive' => Setting::where('is_active', false)->count(),
            'deleted' => Setting::onlyTrashed()->count(),
        ],
    ];
 
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
                'pages' => ['all', 'published', 'draft', 'deleted'],
                'settings' => ['all', 'active', 'inactive', 'deleted'],
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => $expectedStats,
        ]);
});
