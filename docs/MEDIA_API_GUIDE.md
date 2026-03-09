# Media API Documentation

This guide outlines the available endpoints for managing media in the application. All routes are prefixed with `/api`.

## Endpoints

### Single Resource Operations

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/media` | List all media (supports pagination, search, and status filtering). |
| `POST` | `/media` | Upload a new media file. |
| `GET` | `/media/{id}` | Retrieve details of a specific media item. |
| `PATCH` | `/media/{id}` | Update media metadata (name, status). |
| `DELETE` | `/media/{id}` | Soft delete a media item. |
| `PATCH` | `/media/{id}/toggle-status` | Toggle the `is_active` status. |
| `POST` | `/media/{id}/restore` | Restore a soft-deleted media item. |
| `DELETE` | `/media/{id}/force` | Permanently delete a media item. |

### Bulk Operations

| Method | Endpoint | Description |
|---|---|---|
| `DELETE` | `/media/bulk/delete` | Soft delete multiple media items. |
| `POST` | `/media/bulk/restore` | Restore multiple soft-deleted media items. |
| `PATCH` | `/media/bulk/toggle-status` | Toggle status for multiple media items. |
| `DELETE` | `/media/bulk/force-delete` | Permanently delete multiple media items. |

---

## Request Examples

### 1. Upload Media
**Endpoint**: `POST /media`  
**Content-Type**: `multipart/form-data`

| Field | Type | Description |
|---|---|---|
| `file` | file | The file to upload. |
| `collection` | string | (Optional) The collection name (default: `default`). |

### 2. Update Media
**Endpoint**: `PATCH /media/{id}`  
**Body**:
```json
{
    "name": "new_file_name",
    "is_active": true
}
```

### 3. List Media (with Filters)
**Endpoint**: `GET /media?search=report&status=active&per_page=15`

### 4. Bulk Status Toggle
**Endpoint**: `PATCH /media/bulk/toggle-status`  
**Body**:
```json
{
    "ids": [1, 2, 3]
}
```

---

## Response Structure (MediaResource)

All endpoints return a standardized success response.

```json
{
    "success": true,
    "message": "Media retrieved successfully.",
    "data": {
        "id": 1,
        "file_name": "example.png",
        "name": "example",
        "mime_type": "image/png",
        "size": 1024,
        "collection_name": "default",
        "url": "http://localhost/storage/1/example.png",
        "thumbnail_url": "http://localhost/storage/1/conversions/example-thumbnail.png",
        "is_active": true,
        "created_at": "2026-03-04T14:41:24.000000Z"
    }
}
```
