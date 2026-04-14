# System Settings Guide

This guide covers the management of application-wide configurations and settings.

> [!NOTE]
> All settings are managed by the `Setting` module. It supports multiple data types including strings, booleans, and images.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/settings`
- **Logic**: Supports both single-item CRUD and grouped/bulk operations for configuration forms.

---

## ⚙️ Settings Endpoints

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/settings` | List all settings (paginated). | Yes |
| `GET` | `/settings/grouped` | Get settings nested by their `group` key. | Yes |
| `GET` | `/settings/{id}` | Get details of a specific setting. | Yes |
| `POST` | `/settings/bulk` | Update multiple settings in one request. | Yes |
| `PATCH` | `/settings/{id}/toggle-status` | Toggle active status. | Yes |
| `DELETE` | `/settings/{id}` | Soft delete a setting. | Yes |

---

## 🛠️ Bulk Management

The `POST /api/v1/settings/bulk` endpoint is the recommended way to save configuration forms.

### Request Payload
```json
{
    "settings": [
        { "key": "app_name", "value": "My Modular CMS" },
        { "key": "maintenance_mode", "value": false }
    ]
}
```

> [!TIP]
> **Image Uploads**: If a setting is of type `image`, you can send the file in the `value` field using a `multipart/form-data` request. The backend will automatically handle the media conversion and storage.

---

## 📦 Maintenance Operations

Standard maintenance endpoints are available at `/api/v1/settings/`:
- `POST /bulk-destroy`
- `PATCH /bulk-restore`
- `POST /bulk-force-delete`

---

## 📚 Related Guides
- **[Media Library](./MEDIA_API_GUIDE.md)**: Handling images used in settings.
- **[Documentation Index](./README.md)**: Return to main menu.
