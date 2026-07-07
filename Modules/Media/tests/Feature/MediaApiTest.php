<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\Models\User;
use Modules\Media\Models\Media;

test('guests cannot access media endpoints', function () {
    $this->getJson('/api/v1/media')->assertStatus(401);
});

test('authorized user can list media', function () {
    $user = User::factory()->create();

    // Create a dummy media
    Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test-file.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/media')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'file_name', 'name', 'mime_type', 'size', 'collection_name', 'is_active', 'category_id'],
            ],
            'links',
            'meta',
        ]);
});

test('authorized user can upload media', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('avatar.jpg');

    $payload = [
        'file' => $file,
        'name' => 'Avatar Image',
        'collection' => 'profile',
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/media', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Avatar Image')
        ->assertJsonPath('data.collection_name', 'profile');
});

test('authorized user can see media details', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test-file.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/media/{$media->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'test-file');
});

test('authorized user can update media', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'old-name',
        'file_name' => 'test-file.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);

    $payload = [
        'name' => 'new-name',
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/media/{$media->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'new-name');
});

test('authorized user can toggle media status', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test-file.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/media/{$media->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);
});

test('authorized user can soft delete, restore, and force delete a media', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'test-file',
        'file_name' => 'test-file.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/media/{$media->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('media', ['id' => $media->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/media/{$media->id}/restore")
        ->assertStatus(200)
        ->assertJsonPath('data.deleted_at', null);

    $this->assertDatabaseHas('media', ['id' => $media->id, 'deleted_at' => null]);

    // Soft delete again so we can force delete
    $media->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/media/{$media->id}/force")
        ->assertStatus(200);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
});

test('authorized user can perform bulk delete, restore, and force delete on media', function () {
    $user = User::factory()->create();
    $media1 = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'file1',
        'file_name' => 'file1.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    $media2 = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'file2',
        'file_name' => 'file2.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    $ids = [$media1->id, $media2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/media/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('media', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/media/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('media', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again so we can bulk force-delete
    Media::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/media/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('media', ['id' => $id]);
    }
});

test('authorized user can perform bulk toggle status on media', function () {
    $user = User::factory()->create();
    $media1 = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'file1',
        'file_name' => 'file1.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    $media2 = Media::create([
        'model_type' => User::class,
        'model_id' => $user->id,
        'collection_name' => 'default',
        'name' => 'file2',
        'file_name' => 'file2.jpg',
        'disk' => 'public',
        'size' => 1024,
        'manipulations' => [],
        'custom_properties' => [],
        'generated_conversions' => [],
        'responsive_images' => [],
        'is_active' => true,
    ]);
    $ids = [$media1->id, $media2->id];

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/media/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('media', ['id' => $id, 'is_active' => false]);
    }
});
