<?php

use Modules\Acl\Models\User;
use Modules\Blog\Models\Category;

test('guests cannot access categories list', function () {
    $this->getJson('/api/v1/categories')->assertStatus(401);
});

test('authorized user can list categories', function () {
    $user = User::factory()->create();
    Category::factory()->create(['name' => 'Tech']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/categories')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'slug', 'description', 'is_active']
            ],
            'links',
            'meta'
        ]);
});

test('authorized user can create category', function () {
    $user = User::factory()->create();
    $payload = [
        'name' => 'Tech News',
        'type' => Category::TYPE_POST,
        'description' => 'Latest news in technology',
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/categories', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Tech News');
});

test('authorized user can see category details', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Gadgets']);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/categories/{$category->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Gadgets');
});

test('authorized user can update category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Old Name']);
    $payload = [
        'name' => 'New Name',
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/categories/{$category->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'New Name');
});

test('authorized user can toggle category status', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['is_active' => true]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/categories/{$category->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);
});

test('authorized user can soft delete, restore, and force delete a category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/categories/{$category->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('categories', ['id' => $category->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/categories/{$category->id}/restore")
        ->assertStatus(200)
        ->assertJsonPath('data.deleted_at', null);

    $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);

    // Soft delete again to force delete
    $category->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/categories/{$category->id}/force")
        ->assertStatus(200);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('authorized user can perform bulk delete, restore, and force delete on categories', function () {
    $user = User::factory()->create();
    $category1 = Category::factory()->create();
    $category2 = Category::factory()->create();
    $ids = [$category1->id, $category2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/categories/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('categories', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/categories/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('categories', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Category::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/categories/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('categories', ['id' => $id]);
    }
});

test('authorized user can perform bulk toggle status on categories', function () {
    $user = User::factory()->create();
    $category1 = Category::factory()->create(['is_active' => true]);
    $category2 = Category::factory()->create(['is_active' => true]);
    $ids = [$category1->id, $category2->id];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/categories/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('categories', ['id' => $id, 'is_active' => false]);
    }
});
