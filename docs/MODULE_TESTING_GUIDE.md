# Module Testing Guide

This guide explains how to write feature tests when creating a new module in the INI CMS backend.

> [!NOTE]
> The project uses [Pest](https://pestphp.com/) for feature and unit tests. Module tests should stay close to the module they validate and should cover the API behavior exposed under `/api/v1`.

---

## Testing Goals

When you create a new module, add tests that verify:

- guests are rejected from protected endpoints
- authenticated users can access the module endpoints
- create, read, update, and delete flows work as expected
- soft delete, restore, and force delete flows behave correctly
- bulk actions return the expected response and affect the right records
- relationship endpoints work with the intended payload shape

---

## Recommended Location

Place module feature tests inside the module itself:

```text
Modules/<ModuleName>/tests/Feature/<Resource>ApiTest.php
```

Examples:

- `Modules/Acl/tests/Feature/UserApiTest.php`
- `Modules/Blog/tests/Feature/CategoryApiTest.php`

Keep test names descriptive and focused on one behavior per test.

---

## Common Test Pattern

A typical module API test follows this structure:

1. create the authenticated user with a factory
2. seed or create the model under test
3. call the API route with `getJson`, `postJson`, `putJson`, `patchJson`, or `deleteJson`
4. assert the status code
5. assert the JSON structure or database state

Example:

```php
use Modules\Acl\Models\User;
use Modules\Blog\Models\Category;

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
```

---

## Authentication Tests

Always include at least one guest test and one authenticated test for protected endpoints.

### Guest Access

```php
test('guests cannot access users list', function () {
    $this->getJson('/api/v1/users')->assertStatus(401);
});
```

### Authenticated Access

```php
test('authorized user can list users', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/users')
        ->assertStatus(200);
});
```

For modules that expose auth routes, use `/api/v1/auth/*` and assert the expected token payload, profile response, refresh flow, or logout behavior.

---

## CRUD Tests

For a resource endpoint, cover the full CRUD lifecycle:

- list
- create
- show
- update
- delete

Example shape:

```php
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
```

For update tests, assert the database state or returned JSON after the change.

---

## Soft Delete and Restore

If the model uses `SoftDeletes`, add tests for restore and force delete.

Recommended assertions:

- `assertSoftDeleted()` after soft delete
- `assertDatabaseHas()` after restore
- `assertDatabaseMissing()` after force delete

Example:

```php
test('authorized user can soft delete, restore, and force delete a user', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/v1/users/{$target->id}")
        ->assertStatus(200);

    $this->assertSoftDeleted('users', ['id' => $target->id]);

    $this->actingAs($user, 'sanctum')
        ->patchJson("/api/v1/users/{$target->id}/restore")
        ->assertStatus(200);

    $this->assertDatabaseHas('users', ['id' => $target->id, 'deleted_at' => null]);
});
```

---

## Bulk Action Tests

If the module exposes bulk routes, verify that all selected IDs are affected.

Common routes include:

- `POST /api/v1/<resource>/bulk/delete`
- `POST /api/v1/<resource>/bulk/restore`
- `POST /api/v1/<resource>/bulk/force-delete`
- `POST /api/v1/<resource>/bulk/toggle-status`

Example:

```php
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
```

---

## Relationship Actions

Some modules expose relationship endpoints such as assigning roles, syncing permissions, or linking records to another model.

When testing these endpoints:

- create the related records first
- use the expected payload keys from the module request class
- assert the relationship result using the model's helper methods or fresh database state

Example:

```php
test('authorized user can manage user roles', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    Role::create(['name' => 'editor', 'display_name' => ['en' => 'Editor']]);
    Role::create(['name' => 'author', 'display_name' => ['en' => 'Author']]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/users/{$target->id}/assign-roles", ['roles' => ['editor', 'author']])
        ->assertStatus(200);

    $this->assertTrue($target->fresh()->hasAllRoles(['editor', 'author']));
});
```

---

## What To Assert

Prefer assertions that prove the behavior, not just the response status.

Good assertions include:

- `assertJsonPath()` for key values
- `assertJsonStructure()` for response shape
- `assertDatabaseHas()` and `assertDatabaseMissing()` for persistence changes
- `assertSoftDeleted()` for soft delete flows
- model helper methods like `hasRole()` or `hasAllRoles()` when testing relationships

---

## Suggested File Template

Use this as a starting point for a new module resource test:

```php
<?php

use Modules\Acl\Models\User;
use Modules\YourModule\Models\YourModel;

test('guests cannot access your resource list', function () {
    $this->getJson('/api/v1/your-resources')->assertStatus(401);
});

test('authorized user can list your resources', function () {
    $user = User::factory()->create();
    YourModel::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/your-resources')
        ->assertStatus(200);
});
```

---

## Related Guides

- [API Development Guide](./API_DEVELOPMENT_GUIDE.md)
- [Documentation Hub](./README.md)
