# Media Library Guide

This guide outlines the endpoints for managing media in the application, including file uploads, conversions, and bulk management.

> [!NOTE]
> All media operations are handled by the `Media` module. It integrates with `spatie/laravel-medialibrary` to handle file processing and conversions (like WebP optimization).

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/media`
- **Supported Formats**: Images (PNG, JPG, WebP), Documents (PDF, DOCX), etc.

---

## 🖼️ Media Endpoints

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/media` | List all media with filtering and search. | Yes |
| `POST` | `/media` | Upload a new media file. | Yes |
| `GET` | `/media/{id}` | Retrieve details of a specific media item. | Yes |
| `PATCH` | `/media/{id}` | Update metadata (title, status). | Yes |
| `DELETE` | `/media/{id}` | Soft delete a media item. | Yes |
| `PATCH` | `/media/{id}/restore` | Restore a soft-deleted item. | Yes |
| `PATCH` | `/media/{id}/toggle-status` | Toggle the `is_active` status. | Yes |
| `DELETE` | `/media/{id}/force` | Permanently delete the file and record. | Yes |

---

## 📤 Upload Examples

### 1. Simple Upload
**Endpoint**: `POST /api/v1/media`  
**Content-Type**: `multipart/form-data`

| `file` | file | The actual file to upload. |
| `collection` | string | (Optional) Destination collection (default: `default`). |
| `name`| string | (Optional) Internal name for the file. |
| `category_id` | integer | (Optional) ID of the media category. |

### 2. Standard Response
```json
{
    "success": true,
    "message": "Media retrieved successfully.",
    "data": {
        "id": 1,
        "file_name": "example.webp",
        "name": "example",
        "mime_type": "image/webp",
        "size": 1024,
        "url": "http://localhost/storage/1/example.webp",
        "thumbnail_url": "http://localhost/storage/1/conversions/example-thumbnail.webp",
        "preview_url": "http://localhost/storage/1/conversions/example-preview.webp",
        "is_active": true,
        "category_id": 5,
        "category": {
            "id": 5,
            "name": "Nature",
            "slug": "nature",
            "type": "media"
        }
    }
}
```

---

## 🔍 Filtering & Search

The `/media` listing endpoint supports several filters:

- **`search`**: Search by file name or internal name.
- **`category_id`**: Filter by media category.
- **`only_profile_picture`**: Boolean to filter for user avatars.
- **`status`**: `active` or `inactive`.
- **`trashed`**: `only` or `with` for soft-deleted items.

---

## 📦 Bulk Operations

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/media/bulk-destroy` | Soft delete multiple items. |
| `PATCH` | `/media/bulk-restore` | Restore multiple deleted items. |
| `PATCH` | `/media/bulk-toggle-status` | Toggle status for multiple items. |
| `POST` | `/media/bulk-force-delete` | Permanent bulk deletion. |

> [!TIP]
> Bulk operations require an array of `ids` in the request body.

---

## 📚 Related Guides
- **[System Settings](./SETTING_GUIDE.md)**: Configuring media storage paths.
- **[Documentation Index](./README.md)**: Return to main menu.
