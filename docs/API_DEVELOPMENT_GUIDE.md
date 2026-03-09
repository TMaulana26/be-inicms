# INI CMS - API Development Guide

This guide explains how to quickly build robust CRUD API endpoints in the INI CMS backend. We use the `HandlesBulkAndSoftDeletes` trait to handle complex and repetitive logic, while explicitly defining standard CRUD endpoints to ensure perfect API Documentation generation via Dedoc/Scramble.

By utilizing the `HandlesBulkAndSoftDeletes` trait, you instantly get the following endpoints for free:
- `PATCH /api/resource/{id}/restore` (Restore a soft-deleted record)
- `DELETE /api/resource/{id}/force-delete` (Permanently delete)
- `POST /api/resource/bulk-destroy` (Bulk soft-delete)
- `PATCH /api/resource/bulk-restore` (Bulk restore)
- `POST /api/resource/bulk-force-delete` (Bulk permanent delete)
- `PATCH /api/resource/bulk-toggle-status` (Bulk toggle active status)

## How To Create a New CRUD Feature

Let's assume you are creating a new feature for `Category`.

### 1. The Model & Migration
Ensure your model uses Laravel's `SoftDeletes` trait and has an `is_active` boolean column if you want to use the toggling features.
```php
class Category extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['name', 'description', 'is_active'];
}
```

### 2. The Form Requests
Create your standard Laravel Form Requests for validation:
- `IndexCategoryRequest`
- `StoreCategoryRequest`
- `UpdateCategoryRequest`

### 3. The Resource
Create an API Resource to format your JSON responses:
```php
class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // ...
        ];
    }
}
```

### 4. The Service Layer
Your service must handle the business logic and database interactions. It must include methods capable of being targeted by the bulk operations trait.

```php
namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;

class CategoryService
{
    public function index(array $params)
    {
        return Category::query()
            ->paginate($params['per_page'] ?? 10);
    }

    public function store(array $data): Category { /* ... */ }
    public function update(Category $category, array $data): Category { /* ... */ }
    public function delete(Category $category): bool { /* ... */ }
    
    // Required for the Bulk Trait
    public function toggleStatus(Category $category): Category { /* ... */ }
    public function restore(string $id): Category { /* ... */ }
    public function forceDelete(string $id): Category { /* ... */ }
    public function handleBulkOperation(array $ids, string $operation): array { /* ... */ }
}
```

### 5. The Controller

Your controller should extend the standard `Controller` and `use HandlesBulkAndSoftDeletes;`. 

**Crucial Note for Dedoc/Scramble:** You MUST explicitly write the standard CRUD endpoints (`index`, `store`, `update`, `show`, `destroy`) and type-hint the specific Form Request. Scramble uses Static Analysis to read your PHP code. If these requests are hidden behind an abstract parent class, Scramble cannot see them and your API Documentation will not show the correct Request Bodies!

```php
namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Traits\HandlesBulkAndSoftDeletes;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    use HandlesBulkAndSoftDeletes;

    public function __construct(
        protected CategoryService $categoryService
    ) {}

    // Required by the Trait
    protected function getService() { return $this->categoryService; }
    protected function getResourceClass(): string { return CategoryResource::class; }
    protected function getModelName(): string { return 'category'; }
    protected function getEagerLoadRelations(): array { return []; }

    // --- Standard CRUD Methods (Explicit for Scramble) ---

    public function index(IndexCategoryRequest $request): JsonResponse
    {
        $categories = $this->categoryService->index($request->validated());
        return $this->paginatedResponse(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store($request->validated());
        return $this->resourceResponse(new CategoryResource($category), 'Category created successfully.', 201);
    }

    public function show(Category $category): JsonResponse
    {
        return $this->resourceResponse(new CategoryResource($category), 'Category retrieved successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update($category, $request->validated());
        return $this->resourceResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);
        return $this->resourceResponse(new CategoryResource($category), 'Category deleted successfully.');
    }
    
    public function toggleStatus(Category $category): JsonResponse
    {
        $category = $this->categoryService->toggleStatus($category);
        return $this->resourceResponse(new CategoryResource($category), 'Category status toggled successfully.');
    }
}
```

### 6. The Routes
Register the routes in `routes/api.php`. Ensure bulk routes are registered BEFORE the dynamic `/{id}` routes.

```php
Route::prefix('categories')->group(function () {
    // 1. Bulk & Custom Routes first
    Route::post('/bulk-destroy', [CategoryController::class, 'bulkDestroy']);
    Route::post('/bulk-force-delete', [CategoryController::class, 'bulkForceDelete']);
    Route::patch('/bulk-restore', [CategoryController::class, 'bulkRestore']);
    Route::patch('/bulk-toggle-status', [CategoryController::class, 'bulkToggleStatus']);

    // 2. Special single-item routes
    Route::patch('/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('/{id}/force-delete', [CategoryController::class, 'forceDelete']);
    Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus']);

    // 3. Standard CRUD
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::match(['put', 'patch'], '/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});
```

That's it! By following this pattern, you ensure consistent error handling, flawless OpenAPI generation with Scramble, and drastically reduced boilerplate code across the entire INI CMS.
