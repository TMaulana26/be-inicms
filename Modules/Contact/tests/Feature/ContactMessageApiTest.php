<?php

use Modules\Acl\Models\User;
use Modules\Contact\Models\ContactMessage;

test('guests cannot access protected contact endpoints', function () {
    $this->getJson('/api/v1/contact-messages')->assertStatus(401);
});

test('guests can submit contact messages via public endpoint', function () {
    $payload = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This is a test message from a consumer.',
    ];

    $this->postJson('/api/v1/contact-messages', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'John Doe')
        ->assertJsonPath('data.email', 'john@example.com')
        ->assertJsonPath('data.message', 'This is a test message from a consumer.')
        ->assertJsonPath('data.is_read', false)
        ->assertJsonPath('data.is_active', true);

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'message' => 'This is a test message from a consumer.',
    ]);
});

test('guests cannot submit contact messages with invalid data', function () {
    $payload = [
        'name' => '',
        'email' => 'not-an-email',
        'message' => 'shrt',
    ];

    $this->postJson('/api/v1/contact-messages', $payload)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'message']);
});

test('authorized user can list contact messages', function () {
    $user = User::factory()->create();
    ContactMessage::factory()->create(['name' => 'Sarah Connor']);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/contact-messages')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'email', 'message', 'is_read', 'is_active', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ])
        ->assertJsonCount(1, 'data');
});

test('authorized user can see contact message details', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/contact-messages/{$message->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $message->id);
});

test('authorized user can toggle contact message status', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create(['is_active' => true]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/contact-messages/{$message->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);

    $this->assertFalse($message->fresh()->is_active);
});

test('authorized user can toggle contact message read status', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create(['is_read' => false]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/contact-messages/{$message->id}/toggle-read")
        ->assertStatus(200)
        ->assertJsonPath('data.is_read', true);

    $this->assertTrue($message->fresh()->is_read);
});

test('authorized user can soft delete, restore, and force delete a contact message', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create();

    // 1. Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/contact-messages/{$message->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('contact_messages', ['id' => $message->id]);

    // 2. Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/contact-messages/{$message->id}/restore")
        ->assertStatus(200);

    $this->assertDatabaseHas('contact_messages', ['id' => $message->id, 'deleted_at' => null]);

    // Delete again for force delete
    $message->delete();

    // 3. Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/contact-messages/{$message->id}/force-delete")
        ->assertStatus(200);

    $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
});

test('authorized user can bulk delete, restore, and force delete contact messages', function () {
    $user = User::factory()->create();
    $message1 = ContactMessage::factory()->create();
    $message2 = ContactMessage::factory()->create();
    $ids = [$message1->id, $message2->id];

    // 1. Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/contact-messages/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('contact_messages', ['id' => $id]);
    }

    // 2. Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/contact-messages/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('contact_messages', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    ContactMessage::whereIn('id', $ids)->delete();

    // 3. Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/contact-messages/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('contact_messages', ['id' => $id]);
    }
});

test('authorized user can bulk toggle status and read status on contact messages', function () {
    $user = User::factory()->create();
    $message1 = ContactMessage::factory()->create(['is_active' => true, 'is_read' => false]);
    $message2 = ContactMessage::factory()->create(['is_active' => true, 'is_read' => false]);
    $ids = [$message1->id, $message2->id];

    // 1. Bulk Toggle Active Status
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/contact-messages/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('contact_messages', ['id' => $id, 'is_active' => false]);
    }

    // 2. Bulk Toggle Read Status
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/contact-messages/bulk/toggle-read', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('contact_messages', ['id' => $id, 'is_read' => true]);
    }
});
