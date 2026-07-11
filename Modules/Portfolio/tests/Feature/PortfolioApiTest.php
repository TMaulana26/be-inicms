<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Modules\Acl\Models\User;
use Modules\Portfolio\Models\Project;

test('guest can list active projects and see details', function () {
    $activeProject = Project::factory()->create(['is_active' => true]);
    $inactiveProject = Project::factory()->create(['is_active' => false]);

    // Guest index should only return active projects
    $this->getJson('/api/v1/projects')
        ->assertStatus(200)
        ->assertJsonPath('data.0.id', $activeProject->id)
        ->assertJsonCount(1, 'data');

    // Guest show on active project should work
    $this->getJson("/api/v1/projects/{$activeProject->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $activeProject->id);

    // Guest show on inactive project should return 404
    $this->getJson("/api/v1/projects/{$inactiveProject->id}")
        ->assertStatus(404);
});

test('guest cannot modify projects', function () {
    $project = Project::factory()->create();

    $this->postJson('/api/v1/projects', [])->assertStatus(401);
    $this->putJson("/api/v1/projects/{$project->id}", [])->assertStatus(401);
    $this->deleteJson("/api/v1/projects/{$project->id}")->assertStatus(401);
});

test('authorized user can list projects including inactive ones', function () {
    $user = User::factory()->create();
    $activeProject = Project::factory()->create(['is_active' => true]);
    $inactiveProject = Project::factory()->create(['is_active' => false]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/projects')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('authorized user can create project with screenshot', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $payload = [
        'title' => [
            'en' => 'Test Project EN',
            'id' => 'Proyek Tes ID',
        ],
        'slug' => 'test-project-en',
        'category' => 'FULLSTACK',
        'description' => [
            'en' => 'A project description',
            'id' => 'Deskripsi proyek',
        ],
        'tech_stack' => ['Laravel', 'React'],
        'github_url' => 'https://github.com/test',
        'demo_url' => 'https://demo.com',
        'is_active' => true,
        'screenshot' => UploadedFile::fake()->image('screenshot.png'),
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects', $payload)
        ->assertStatus(201)
        ->assertJsonPath('data.title.en', 'Test Project EN')
        ->assertJsonPath('data.title.id', 'Proyek Tes ID')
        ->assertJsonPath('data.slug', 'test-project-en');

    /** @var Project $project */
    $project = Project::latest()->first();
    $this->assertNotNull($project->getFirstMedia('screenshot'));
    $this->assertNotNull($response->json('data.screenshot_url'));
});

test('authorized user can update project and change screenshot', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $project = Project::factory()->create(['title' => ['en' => 'Old Title', 'id' => 'Judul Lama']]);

    $payload = [
        'title' => [
            'en' => 'New Title',
            'id' => 'Judul Baru',
        ],
        'screenshot' => UploadedFile::fake()->image('new-screenshot.png'),
    ];

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/v1/projects/{$project->id}", $payload)
        ->assertStatus(200)
        ->assertJsonPath('data.title.en', 'New Title');

    $this->assertNotNull($project->fresh()->getFirstMedia('screenshot'));
});

test('authorized user can toggle project active status', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['is_active' => true]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/projects/{$project->id}/toggle-status")
        ->assertStatus(200)
        ->assertJsonPath('data.is_active', false);

    $this->assertFalse($project->fresh()->is_active);
});

test('authorized user can soft delete, restore, and force delete a project', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $project = Project::factory()->create();
    $project->addMedia(UploadedFile::fake()->image('temp.png'))->toMediaCollection('screenshot');

    // 1. Soft Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('portfolio_projects', ['id' => $project->id]);

    // 2. Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/projects/{$project->id}/restore")
        ->assertStatus(200);

    $this->assertDatabaseHas('portfolio_projects', ['id' => $project->id, 'deleted_at' => null]);

    // Delete again for force delete
    $project->delete();

    // 3. Force Delete
    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/projects/{$project->id}/force-delete")
        ->assertStatus(200);

    $this->assertDatabaseMissing('portfolio_projects', ['id' => $project->id]);
});

test('authorized user can bulk delete, restore, and force delete projects', function () {
    $user = User::factory()->create();
    $project1 = Project::factory()->create();
    $project2 = Project::factory()->create();
    $ids = [$project1->id, $project2->id];

    // 1. Bulk Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects/bulk/delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertSoftDeleted('portfolio_projects', ['id' => $id]);
    }

    // 2. Bulk Restore
    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/projects/bulk/restore', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('portfolio_projects', ['id' => $id, 'deleted_at' => null]);
    }

    // Soft delete again
    Project::whereIn('id', $ids)->delete();

    // 3. Bulk Force Delete
    $this->actingAs($user, 'sanctum')
        ->postJson('/api/v1/projects/bulk/force-delete', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('portfolio_projects', ['id' => $id]);
    }
});

test('authorized user can bulk toggle status on projects', function () {
    $user = User::factory()->create();
    $project1 = Project::factory()->create(['is_active' => true]);
    $project2 = Project::factory()->create(['is_active' => true]);
    $ids = [$project1->id, $project2->id];

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/v1/projects/bulk/toggle-status', ['ids' => $ids])
        ->assertStatus(200);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('portfolio_projects', ['id' => $id, 'is_active' => false]);
    }
});
