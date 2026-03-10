# INI CMS - Index Query Guide

This guide explains how to use the `HandlesIndexQuery` trait to build consistent, powerful, and reusable listing endpoints in your Services.

By using this trait, you ensures that every `index` endpoint in the system follows the same standards for filtering, searching, sorting, and pagination.

## Core Features

- **Soft Delete Filtering**: Automatically handles `trashed=only` and `trashed=with`.
- **Status Filtering**: Handles `status=active` and `status=inactive`.
- **Multi-column Search**: Dynamic "OR" searching across any number of specified columns.
- **Consistent Sorting**: Standard handling of `sort_by` and `sort_order`.
- **Advanced Pagination**: Support for custom `per_page` values, including `-1` to show all data.
- **Customizable**: An optional `modifyQuery` closure for service-specific logic.

## Usage in Services

To use the trait, include it in your Service class and call `handleIndexQuery` inside your `index` method.

```php
namespace App\Services;

use App\Models\Product;
use App\Traits\HandlesIndexQuery;

class ProductService
{
    use HandlesIndexQuery;

    public function index(array $params)
    {
        return $this->handleIndexQuery(
            Product::query(),    // 1. The base query
            $params,             // 2. The validated request parameters
            ['name', 'sku'],     // 3. Columns to search in
            null,                // 4. (Optional) Custom modification closure
            15                   // 5. (Optional) Default per_page (default is 10)
        );
    }
}
```

## Advanced Usage: Custom Query Modification

If you need to add specific logic (like eager loading or extra filters), use the `$modifyQuery` parameter:

```php
public function index(array $params)
{
    return $this->handleIndexQuery(
        Product::query(),
        $params,
        ['name'],
        function ($query) use ($params) {
            // Eager load relations
            $query->with('category');

            // Add custom filters
            if (isset($params['category_id'])) {
                $query->where('category_id', $params['category_id']);
            }
        }
    );
}
```

## Special Note: Show All Data

Passing `per_page=-1` in the request will automatically bypass standard pagination and return all matching records in a single page. This is useful for dropdowns or small tables where pagination is not desired.

## Benefits

1. **DRY (Don't Repeat Yourself)**: You don't have to write the same 20 lines of query logic in every service.
2. **Consistency**: All APIs behave exactly the same way for frontend developers.
3. **Safety**: Searching is automatically wrapped in parentheses (logical grouping) to prevent breaking other filters.
