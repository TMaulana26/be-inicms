<?php

use Modules\Acl\Models\User;
use Modules\Acl\Models\Role;
use Modules\Acl\Models\Permission;
use Modules\Media\Models\Media;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;
use Modules\Page\Models\Page;
use Modules\Menu\Models\Menu;
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

    // Categories
    Category::factory()->create(['is_active' => true]);
    Category::factory()->create(['is_active' => false]);
    $deletedCategory = Category::factory()->create();
    $deletedCategory->delete();

    // Posts
    Post::factory()->create(['status' => 'published']);
    Post::factory()->create(['status' => 'draft']);
    $deletedPost = Post::factory()->create();
    $deletedPost->delete();

    // Pages
    Page::factory()->create(['status' => 'published']);
    Page::factory()->create(['status' => 'draft']);
    $deletedPage = Page::factory()->create();
    $deletedPage->delete();

    // Menus
    Menu::create(['name' => 'Menu 1', 'slug' => 'menu-1', 'is_active' => true]);
    Menu::create(['name' => 'Menu 2', 'slug' => 'menu-2', 'is_active' => false]);
    $deletedMenu = Menu::create(['name' => 'Menu 3', 'slug' => 'menu-3', 'is_active' => true]);
    $deletedMenu->delete();

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
            'all' => Media::count(),
            'active' => Media::where('is_active', true)->count(),
            'inactive' => Media::where('is_active', false)->count(),
            'deleted' => Media::onlyTrashed()->count(),
        ],
        'categories' => [
            'all' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'deleted' => Category::onlyTrashed()->count(),
        ],
        'posts' => [
            'all' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'deleted' => Post::onlyTrashed()->count(),
        ],
        'pages' => [
            'all' => Page::count(),
            'published' => Page::where('status', 'published')->count(),
            'draft' => Page::where('status', 'draft')->count(),
            'deleted' => Page::onlyTrashed()->count(),
        ],
        'menus' => [
            'all' => Menu::count(),
            'active' => Menu::where('is_active', true)->count(),
            'inactive' => Menu::where('is_active', false)->count(),
            'deleted' => Menu::onlyTrashed()->count(),
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
                'categories' => ['all', 'active', 'inactive', 'deleted'],
                'posts' => ['all', 'published', 'draft', 'deleted'],
                'pages' => ['all', 'published', 'draft', 'deleted'],
                'menus' => ['all', 'active', 'inactive', 'deleted'],
                'settings' => ['all', 'active', 'inactive', 'deleted'],
            ],
        ])
        ->assertJson([
            'success' => true,
            'data' => $expectedStats,
        ]);
});
