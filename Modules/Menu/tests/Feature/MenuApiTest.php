<?php

use Modules\Acl\Models\User;
use Modules\Menu\Models\Menu;

test('guests cannot access menu endpoints', function () {
    $this->getJson('/api/v1/menus')->assertStatus(401);
});

test('authorized user can list menus', function () {
    $user = User::factory()->create();
    Menu::create([
        'name' => ['en' => 'Main Menu'],
        'slug' => 'main-menu',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/menus')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'slug', 'title', 'description', 'url', 'target', 'order', 'is_active', 'children']
            ]
        ]);
});

test('authorized user can create menu with children', function () {
    $user = User::factory()->create();
    $payload = [
        'name' => 'Main Navigation',
        'slug' => 'main-nav',
        'description' => 'Top header menu',
        'is_active' => true,
        'children' => [
            [
                'title' => 'Home',
                'url' => '/',
                'target' => '_self',
                'order' => 1,
            ],
            [
                'title' => 'About Us',
                'url' => '/about',
                'target' => '_self',
                'order' => 2,
            ]
        ]
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/menus', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Main Navigation')
        ->assertJsonCount(2, 'data.children');

    $this->assertDatabaseHas('menus', ['slug' => 'main-nav']);
    $this->assertDatabaseHas('menus', ['url' => '/about']);
});

test('authorized user can see menu details by ID or slug', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'name' => 'Sidebar Menu',
        'slug' => 'sidebar-menu',
        'is_active' => true,
    ]);

    // View by ID
    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/menus/{$menu->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.slug', 'sidebar-menu');

    // View by Slug
    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/menus/{$menu->slug}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $menu->id);
});

test('authorized user can update menu and its children', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'name' => 'Header Menu',
        'slug' => 'header-menu',
        'is_active' => true,
    ]);
    
    $child = Menu::create([
        'parent_id' => $menu->id,
        'name' => 'Old Child',
        'title' => 'Old Child',
        'slug' => 'old-child',
        'url' => '/old-path',
        'is_active' => true,
    ]);

    // Update payload: rename menu, update existing child, and add new child
    $payload = [
        'name' => 'New Header Menu',
        'children' => [
            [
                'id' => $child->id,
                'title' => 'Updated Child',
                'url' => '/new-path',
                'order' => 1,
            ],
            [
                'title' => 'New Child Link',
                'url' => '/new-link',
                'order' => 2,
            ]
        ]
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/menus/{$menu->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'New Header Menu')
        ->assertJsonCount(2, 'data.children');

    $this->assertDatabaseHas('menus', ['id' => $child->id, 'url' => '/new-path']);
    $this->assertDatabaseHas('menus', ['url' => '/new-link']);
});

test('authorized user can toggle menu status', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'name' => 'Test Menu',
        'slug' => 'test-menu',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/menus/{$menu->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);
});

test('authorized user can soft delete, restore, and force delete a menu', function () {
    $user = User::factory()->create();
    $menu = Menu::create([
        'name' => 'Test Menu',
        'slug' => 'test-menu',
        'is_active' => true,
    ]);

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/menus/{$menu->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('menus', ['id' => $menu->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/menus/{$menu->id}/restore")
        ->assertStatus(200)
        ->assertJsonPath('data.deleted_at', null);

    $this->assertDatabaseHas('menus', ['id' => $menu->id, 'deleted_at' => null]);

    // Soft delete again so we can force delete
    $menu->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/menus/{$menu->id}/force-delete")
        ->assertStatus(200);

    $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
});

test('authorized user can perform bulk operations on menus', function () {
    $user = User::factory()->create();
    $menu1 = Menu::create([
        'name' => 'Menu 1',
        'slug' => 'menu-1',
        'is_active' => true,
    ]);
    $menu2 = Menu::create([
        'name' => 'Menu 2',
        'slug' => 'menu-2',
        'is_active' => true,
    ]);
    $ids = [$menu1->id, $menu2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/menus/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('menus', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/menus/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('menus', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Menu::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/menus/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('menus', ['id' => $id]);
    }
});

test('authorized user can perform bulk toggle status on menus', function () {
    $user = User::factory()->create();
    $menu1 = Menu::create([
        'name' => 'Menu 1',
        'slug' => 'menu-1',
        'is_active' => true,
    ]);
    $menu2 = Menu::create([
        'name' => 'Menu 2',
        'slug' => 'menu-2',
        'is_active' => true,
    ]);
    $ids = [$menu1->id, $menu2->id];

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/menus/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('menus', ['id' => $id, 'is_active' => false]);
    }
});
