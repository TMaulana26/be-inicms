# Development - Indexing & Queries

This guide explains how to use the `HandlesIndexQuery` trait to build powerful, consistent listing endpoints within your modular services.

> [!NOTE]
> This trait is the backbone of our API's search, filter, and pagination system. It ensures a unified experience for frontend developers across all modules.

---

## 🏗️ Core Trait Integration

Include the trait in your Service class (located in `Modules/{Module}/app/Services`) and call `handleIndexQuery` inside your `index` method.

### Basic Implementation

```php
namespace Modules\Category\Services;

use Modules\Category\Models\Category;
use App\Traits\HandlesIndexQuery;

class CategoryService
{
    use HandlesIndexQuery;

    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Category::query(),    // 1. Base Query
            $params,              // 2. Request Parameters
            ['name', 'slug'],     // 3. Search Columns
            null,                 // 4. (Optional) Custom Logic Closure
            15                    // 5. (Optional) Default Items Per Page
        );
    }
}
```

---

## 🔍 Supported Query Parameters

Frontend developers can use these parameters on any endpoint implementing this trait:

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `search` | string | Full-text search across the columns specified in the Service. |
| `status` | string | Filter by `active` or `inactive`. |
| `type` | string | (Categories) Filter by taxonomy type (`post` or `media`). |
| `category_id` | integer | (Posts/Media) Filter records by their associated category. |
| `trashed` | string | Use `only` for deleted items, or `with` to include both. |
| `per_page` | integer | Number of records. Use `-1` to disable pagination. |
| `sort_by` | string | The column name to sort by (default: `id`). |
| `sort_order` | string | Either `asc` or `desc` (default: `desc`). |

---

## 🛠️ Advanced Modification

Use the fourth parameter (closure) to add eager loading or context-specific filters:

```php
return $this->handleIndexQuery(
    Post::query(),
    $params,
    ['title', 'content'],
    function ($query) use ($params) {
        $query->with('author', 'tags'); // Eager load relations
        
        if (isset($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }
    }
);
```

> [!TIP]
> Always use `$query->when()` inside the modification closure for cleaner conditional logic.

---

## 📚 Related Guides
- **[API Development Guide](./API_DEVELOPMENT_GUIDE.md)**: Standard modular development.
- **[Documentation Index](./README.md)**: Return to main menu.
