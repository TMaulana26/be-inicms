# Development - API & Modular Workflow

This guide explains how to build robust, modular CRUD API endpoints using the **nwidart/laravel-modules** architecture.

> [!IMPORTANT]
> All new features MUST be created within a module. Standard Laravel directory structures (e.g., `app/Models`) should only be used for globally shared resources.

---

## 🏗️ Technical Architecture

We utilize specialized traits to standardize complex operations:
- **`HandlesIndexQuery`**: Global filtering, advanced search, and pagination.
- **`HandlesBulkAndSoftDeletes`**: Bulk operations (restore, toggle, force-delete) with minimal code.
- **`ApiResponse`**: Consistent JSON response structures.

---

## 🛠️ Module Creation Workflow

### 1. Initialize the Module
Generate the modular folder structure:
```bash
php artisan module:make <ModuleName>
```

### 2. Model & Migration
Generate the model within the module's namespace:
```bash
php artisan module:make-model <ModelName> <ModuleName> -m
```

> [!TIP]
> Ensure your model uses the `SoftDeletes` trait to support the bulk restoration features.

### 3. Service Layer
Services handle business logic. Use the custom modular generator:
```bash
php artisan module:make-service <ServiceName> <ModuleName>
```

### 4. Controller & Trait Integration
Controllers should extend the base `Controller` and use the bulk trait:

```php
namespace Modules\<ModuleName>\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\HandlesBulkAndSoftDeletes;

class <ModelName>Controller extends Controller
{
    use HandlesBulkAndSoftDeletes;
    // ...
}
```

---

## 📄 API Documentation (Scramble)

We use **Dedoc/Scramble** for automated OpenAPI generation. To ensure perfect documentation:

1.  **Explicit Methods**: Always define `index`, `store`, `show`, `update`, and `destroy` explicitly.
2.  **Type-hinting**: Always type-hint specific `FormRequest` classes in your method signatures.
3.  **Resources**: Always return an API Resource (e.g., `UserResource`) rather than a raw model.

---

## 🔗 Route Registration

Route registration must follow this specific order to avoid parameter collisions:

```php
Route::prefix('<resource>')->group(function () {
    // 1. Bulk & Custom Routes (Priority)
    Route::post('/bulk-destroy', [Controller::class, 'bulkDestroy']);
    Route::patch('/bulk-restore', [Controller::class, 'bulkRestore']);

    // 2. Special Single-Item Routes
    Route::patch('/{id}/restore', [Controller::class, 'restore']);

    // 3. Standard API Resource
    Route::apiResource('/', Controller::class);
});
```

---

## 📚 Related Guides
- **[Indexing & Queries](./INDEX_QUERY_GUIDE.md)**: Details on filtering and sorting.
- **[Documentation Index](./README.md)**: Return to main menu.
