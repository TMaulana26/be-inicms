<?php

use Modules\Acl\Models\User;
use Modules\Skill\Models\Skill;

test('guest can list active skills and see details on both v1 and flat api', function () {
    // Create skills with specific order to test sorting
    $activeSecond = Skill::factory()->create(['is_active' => true, 'order' => 10, 'name' => 'Second']);
    $activeFirst = Skill::factory()->create(['is_active' => true, 'order' => 5, 'name' => 'First']);
    $inactive = Skill::factory()->create(['is_active' => false, 'order' => 1]);

    // 1. Test /api/v1/skills
    $this->getJson('/api/v1/skills')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $activeFirst->id)
        ->assertJsonPath('data.1.id', $activeSecond->id);

    // 2. Test flat /api/skills
    $this->getJson('/api/skills')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $activeFirst->id)
        ->assertJsonPath('data.1.id', $activeSecond->id);

    // Guest show on active skill should work
    $this->getJson("/api/v1/skills/{$activeFirst->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $activeFirst->id);

    // Guest show on inactive skill should return 404
    $this->getJson("/api/v1/skills/{$inactive->id}")
        ->assertStatus(404);
});

test('guest cannot modify skills', function () {
    $skill = Skill::factory()->create();

    $this->postJson('/api/v1/skills', [])->assertStatus(401);
    $this->putJson("/api/v1/skills/{$skill->id}", [])->assertStatus(401);
    $this->deleteJson("/api/v1/skills/{$skill->id}")->assertStatus(401);
});

test('authorized user can list skills including inactive ones', function () {
    $user = User::factory()->create();
    $activeSkill = Skill::factory()->create(['is_active' => true]);
    $inactiveSkill = Skill::factory()->create(['is_active' => false]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/skills')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('authorized user can create skill', function () {
    $user = User::factory()->create();

    $payload = [
        'name' => 'Laravel Modular',
        'category' => 'backend',
        'order' => 1,
        'is_active' => true,
    ];

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/skills', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.name', 'Laravel Modular')
        ->assertJsonPath('data.category', 'backend')
        ->assertJsonPath('data.order', 1);

    $this->assertDatabaseHas('skills', [
        'name' => 'Laravel Modular',
        'category' => 'backend',
    ]);
});

test('authorized user can update skill', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create(['name' => 'Old Name']);

    $payload = [
        'name' => 'New Name',
    ];

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/skills/{$skill->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'New Name');

    $this->assertDatabaseHas('skills', [
        'id' => $skill->id,
        'name' => 'New Name',
    ]);
});

test('authorized user can toggle skill active status', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create(['is_active' => true]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/skills/{$skill->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);

    $this->assertFalse($skill->fresh()->is_active);
});

test('authorized user can soft delete, restore, and force delete a skill', function () {
    $user = User::factory()->create();
    $skill = Skill::factory()->create();

    // 1. Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/skills/{$skill->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('skills', ['id' => $skill->id]);

    // 2. Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/skills/{$skill->id}/restore")
        ->assertStatus(200);

    $this->assertDatabaseHas('skills', ['id' => $skill->id, 'deleted_at' => null]);

    // Delete again for force delete
    $skill->delete();

    // 3. Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/skills/{$skill->id}/force-delete")
        ->assertStatus(200);

    $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
});

test('authorized user can bulk delete, restore, and force delete skills', function () {
    $user = User::factory()->create();
    $skill1 = Skill::factory()->create();
    $skill2 = Skill::factory()->create();
    $ids = [$skill1->id, $skill2->id];

    // 1. Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/skills/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('skills', ['id' => $id]);
    }

    // 2. Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/skills/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('skills', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Skill::whereIn('id', $ids)->delete();

    // 3. Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/skills/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('skills', ['id' => $id]);
    }
});

test('authorized user can bulk toggle status on skills', function () {
    $user = User::factory()->create();
    $skill1 = Skill::factory()->create(['is_active' => true]);
    $skill2 = Skill::factory()->create(['is_active' => true]);
    $ids = [$skill1->id, $skill2->id];

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/skills/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('skills', ['id' => $id, 'is_active' => false]);
    }
});
