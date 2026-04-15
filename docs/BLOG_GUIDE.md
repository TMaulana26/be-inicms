# Blog Module Guide

The **Blog** module provides a full-featured content management system for managing categories and posts.

## Features

- **Categorized Content**: Organize posts into logical categories.
- **Rich Media**: Support for featured images using Spatie MediaLibrary.
- **Slug Support**: Automatic unique slug generation for SEO-friendly URLs.
- **Soft Deletes**: Safety net for accidental deletions with restoration support.
- **Bulk Operations**: Efficiently manage multiple items (delete, restore, force delete).
- **Status Management**: Support for `draft` and `published` workflows.

## API Endpoints

### Categories

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/categories` | List all categories. Supports `type` filter (defaults to `post`). |
| POST | `/api/v1/categories` | Create a new category. Specify `type` (`post` or `media`). |
| GET | `/api/v1/categories/{id}` | Show category details |
| PUT | `/api/v1/categories/{id}` | Update category |
| DELETE | `/api/v1/categories/{id}` | Soft delete category |
| PATCH | `/api/v1/categories/{id}/toggle-status` | Toggle active status |
| POST | `/api/v1/categories/{id}/restore` | Restore soft-deleted category |
| DELETE | `/api/v1/categories/{id}/force` | Permanent deletion |

> [!NOTE]
> The Category system is shared across modules. Use the `type` parameter (`post` for Blog, `media` for Media Library) to scope your taxonomies.

### Posts

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/posts` | List all posts (filtered/paginated) |
| POST | `/api/v1/posts` | Create a new post (supports image upload) |
| GET | `/api/v1/posts/{id}` | Show post details |
| PUT | `/api/v1/posts/{id}` | Update post |
| DELETE | `/api/v1/posts/{id}` | Soft delete post |
| POST | `/api/v1/posts/{id}/restore` | Restore soft-deleted post |
| DELETE | `/api/v1/posts/{id}/force` | Permanent deletion |

## Bulk Actions

All resources support bulk operations via the `/bulk` prefix (e.g., `/api/v1/posts/bulk/delete`).

> [!TIP]
> Use the `trashed` parameter (`only`, `with`) in GET requests to manage soft-deleted items.
