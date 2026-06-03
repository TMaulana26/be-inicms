<?php

use Modules\Acl\Models\User;
use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

test('guests cannot access posts list', function () {
    $this->getJson('/api/v1/posts')->assertStatus(401);
});

test('authorized user can list posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/posts')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'slug', 'summary', 'content', 'status', 'is_featured', 'category', 'author']
            ],
            'links',
            'meta'
        ]);
});

test('authorized user can create post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $payload = [
        'category_id' => $category->id,
        'title' => 'My First Post',
        'content' => 'This is the body of the post.',
        'status' => 'published',
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/posts', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'My First Post');
});

test('authorized user can see post details', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id, 'title' => 'My Specific Post']);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/posts/{$post->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.title', 'My Specific Post');
});

test('authorized user can update post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id, 'title' => 'Old Title']);
    $payload = [
        'title' => 'New Title',
        'content' => 'Updated content here.',
        'status' => 'draft',
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/posts/{$post->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.title', 'New Title');
});

test('authorized user can soft delete, restore, and force delete a post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/posts/{$post->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('posts', ['id' => $post->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/posts/{$post->id}/restore")
        ->assertStatus(200)
        ->assertJsonPath('data.deleted_at', null);

    $this->assertDatabaseHas('posts', ['id' => $post->id, 'deleted_at' => null]);

    // Soft delete again to force delete
    $post->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/posts/{$post->id}/force")
        ->assertStatus(200);

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('authorized user can perform bulk delete, restore, and force delete on posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post1 = Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);
    $post2 = Post::factory()->create(['category_id' => $category->id, 'user_id' => $user->id]);
    $ids = [$post1->id, $post2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/posts/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('posts', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/posts/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('posts', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Post::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/posts/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('posts', ['id' => $id]);
    }
});
