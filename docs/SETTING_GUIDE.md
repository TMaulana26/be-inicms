# Frontend Integration - Settings

This guide covers application settings management.

## Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/settings` | List settings (paginated). |
| `GET` | `/settings/grouped` | Get all settings grouped by their `group` (e.g., identity, contact). |
| `GET` | `/settings/{id}` | Get specific setting. |
| `POST` | `/settings/bulk` | Update multiple settings at once. |
| `DELETE` | `/settings/{id}` | Delete setting. |

---

## Grouped Settings (Preferred for UI)
The `GET /settings/grouped` endpoint is the primary way to fetch settings for a configuration page. It returns a nested object where keys are the group names.

---

## Bulk Update
Use this to save an entire form of settings.

**Endpoint**: `POST /settings/bulk`  
**Payload**:
```json
{
    "settings": [
        { "key": "app_name", "value": "My Modified CMS" },
        { "key": "contact_email", "value": "new@example.com" }
    ]
}
```

---

## Bulk Operations
Standard bulk endpoints are available:
- `POST /settings/bulk-destroy`
- `PATCH /settings/bulk-restore`
- `PATCH /settings/bulk-toggle-status`
- `POST /settings/bulk-force-delete`

## Filtering and Searching
- **Search columns**: `key`, `name`.
- **Custom Filters**: `group` (e.g., `/api/v1/settings?group=general`).
