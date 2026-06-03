<?php

use Modules\Acl\Models\User;
use Modules\Setting\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guests cannot access setting endpoints', function () {
    $this->getJson('/api/v1/settings')->assertStatus(401);
});

test('authorized user can list settings', function () {
    $user = User::factory()->create();
    Setting::create([
        'key' => 'site_title',
        'name' => 'Site Title',
        'value' => 'My Awesome Site',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/settings')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'key', 'name', 'value', 'type', 'group', 'description', 'casted_value']
            ]
        ]);
});

test('authorized user can get settings grouped by group', function () {
    $user = User::factory()->create();
    Setting::create([
        'key' => 'site_title',
        'name' => 'Site Title',
        'value' => 'My Site',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    Setting::create([
        'key' => 'smtp_host',
        'name' => 'SMTP Host',
        'value' => 'smtp.mailtrap.io',
        'type' => 'text',
        'group' => 'email',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/settings/grouped')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'general' => [
                    '*' => ['id', 'key', 'name', 'value', 'type', 'group', 'casted_value']
                ],
                'email' => [
                    '*' => ['id', 'key', 'name', 'value', 'type', 'group', 'casted_value']
                ]
            ]
        ]);
});

test('authorized user can see setting details', function () {
    $user = User::factory()->create();
    $setting = Setting::create([
        'key' => 'maintenance_mode',
        'name' => 'Maintenance Mode',
        'value' => 'false',
        'type' => 'boolean',
        'group' => 'general',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson("/api/v1/settings/{$setting->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.key', 'maintenance_mode')
        ->assertJsonPath('data.casted_value', false);
});

test('authorized user can bulk update settings', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $settingText = Setting::create([
        'key' => 'site_title',
        'name' => 'Site Title',
        'value' => 'Old Title',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    $settingImage = Setting::create([
        'key' => 'site_logo',
        'name' => 'Site Logo',
        'value' => null,
        'type' => 'image',
        'group' => 'general',
        'is_active' => true,
    ]);

    $payload = [
        'settings' => [
            [
                'key' => 'site_title',
                'value' => 'New Awesome Title',
            ],
            [
                'key' => 'site_logo',
                'value' => UploadedFile::fake()->image('logo.png'),
            ]
        ]
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/settings/bulk-update', $payload)
        ->assertStatus(200);

    $this->assertEquals('New Awesome Title', $settingText->fresh()->value);
    $this->assertNotNull($settingImage->fresh()->value);
});

test('authorized user can toggle single setting status', function () {
    $user = User::factory()->create();
    $setting = Setting::create([
        'key' => 'test_toggle',
        'name' => 'Test Toggle',
        'value' => '123',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/settings/{$setting->id}/toggle-status")
        ->assertStatus(200);

    $this->assertFalse((bool) $setting->fresh()->is_active);
});

test('authorized user can soft delete, restore, and force delete a setting', function () {
    $user = User::factory()->create();
    $setting = Setting::create([
        'key' => 'temp_setting',
        'name' => 'Temp Setting',
        'value' => 'val',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);

    // Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/settings/{$setting->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('settings', ['id' => $setting->id]);

    // Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/settings/{$setting->id}/restore")
        ->assertStatus(200);

    $this->assertDatabaseHas('settings', ['id' => $setting->id, 'deleted_at' => null]);

    // Soft delete again so we can force delete
    $setting->delete();

    // Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/settings/{$setting->id}/force-delete")
        ->assertStatus(200);

    $this->assertDatabaseMissing('settings', ['id' => $setting->id]);
});

test('authorized user can perform bulk operations on settings', function () {
    $user = User::factory()->create();
    $setting1 = Setting::create([
        'key' => 'set_1',
        'name' => 'Set 1',
        'value' => 'val1',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    $setting2 = Setting::create([
        'key' => 'set_2',
        'name' => 'Set 2',
        'value' => 'val2',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    $ids = [$setting1->id, $setting2->id];

    // Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/settings/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('settings', ['id' => $id]);
    }

    // Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/settings/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('settings', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Setting::whereIn('id', $ids)->delete();

    // Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/settings/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('settings', ['id' => $id]);
    }
});

test('authorized user can perform bulk toggle status on settings', function () {
    $user = User::factory()->create();
    $setting1 = Setting::create([
        'key' => 'set_1',
        'name' => 'Set 1',
        'value' => 'val1',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    $setting2 = Setting::create([
        'key' => 'set_2',
        'name' => 'Set 2',
        'value' => 'val2',
        'type' => 'text',
        'group' => 'general',
        'is_active' => true,
    ]);
    $ids = [$setting1->id, $setting2->id];

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/settings/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('settings', ['id' => $id, 'is_active' => false]);
    }
});
