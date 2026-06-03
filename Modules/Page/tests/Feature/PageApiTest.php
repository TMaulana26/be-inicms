<?php

use Modules\Acl\Models\User;
use Modules\Page\Models\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access page endpoints', function () {
    $this->getJson('/api/v1/pages')->assertStatus(401);
});

test('authorized user can list pages', function () {
    $user = User::factory()->create();
    Page::factory()->create(['title' => 'Home Page', 'user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/pages')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'author', 'title', 'slug', 'content', 'status', 'page_image']
            ],
            'links',
            'meta'
        ]);
});

test('authorized user can create page with image', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('page.jpg');

    $payload = [
        'title' => 'About Us',
        'content' => 'This is the about us page content.',
        'status' => 'published',
        'page_image' => $image,
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/pages', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'About Us')
        ->assertJsonPath('data.status', 'published');

    $this->assertDatabaseHas('pages', ['slug' => 'about-us']);
});

test('authorized user can see page details', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create(['title' => 'Services', 'user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/pages/{$page->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.title', 'Services');
});

test('authorized user can update page', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create(['title' => 'Contact Us', 'user_id' => $user->id]);

    $payload = [
        'title' => 'Contact Us Today',
        'content' => 'Updated contact details.',
        'status' => 'draft',
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/pages/{$page->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.title', 'Contact Us Today')
        ->assertJsonPath('data.status', 'draft');
});

test('authorized user can soft delete, restore, and force delete a page', function () {
    $user = User::factory()->create();
    $page = Page::factory()->create(['user_id' => $user->id]);

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/pages/{$page->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('pages', ['id' => $page->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/pages/{$page->id}/restore")
        ->assertStatus(200)
        ->assertJsonPath('data.deleted_at', null);

    $this->assertDatabaseHas('pages', ['id' => $page->id, 'deleted_at' => null]);

    // Soft delete again so we can force delete
    $page->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/pages/{$page->id}/force")
        ->assertStatus(200);

    $this->assertDatabaseMissing('pages', ['id' => $page->id]);
});

test('authorized user can perform bulk delete, restore, and force delete on pages', function () {
    $user = User::factory()->create();
    $page1 = Page::factory()->create(['user_id' => $user->id]);
    $page2 = Page::factory()->create(['user_id' => $user->id]);
    $ids = [$page1->id, $page2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/pages/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('pages', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/pages/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('pages', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Page::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/pages/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('pages', ['id' => $id]);
    }
});
