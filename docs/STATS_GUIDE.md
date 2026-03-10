# Frontend Integration - Stats

This guide covers the dashboard statistics endpoint.

## Endpoint

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/stats` | Returns a summary of record counts across the system. |

---

## Response Structure

**Endpoint**: `GET /stats`  
**Response**:
```json
{
    "success": true,
    "message": "Stats retrieved successfully.",
    "data": {
        "users": 11,
        "roles": 4,
        "permissions": 11,
        "menus": 1,
        "menu_items": 7,
        "media": 0,
        "settings": 6
    }
}
```

This endpoint is typically used to populate summary cards on the admin dashboard.
