# Dashboard Stats Guide

This guide covers the system-wide reporting and statistics endpoint used for the admin dashboard.

> [!NOTE]
> Stats are aggregated by the `Dashboard` module, which pulls real-time counts from various system modules (Acl, Media, etc.).

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/stats`
- **Auth**: Requires a valid user session (Sanctum).

---

## 📊 Stats Endpoint

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/stats` | Returns nested record counts (active, inactive, deleted). | Yes |

### Response Structure
The endpoint returns a detailed breakdown of entity states to enable fine-grained dashboard reporting.

```json
{
    "success": true,
    "message": "Stats retrieved successfully.",
    "data": {
        "users": {
            "all": 11,
            "active": 10,
            "inactive": 1,
            "deleted": 0
        },
        "roles": { ... },
        "permissions": { ... },
        "media": { ... }
    }
}
```

> [!IMPORTANT]
> The counts refer to records within the specific module's scope. For example, `deleted` counts specifically refer to items currently in the "Trash" (Soft Deleted).

---

## 📚 Related Guides
- **[User Management](./USER_GUIDE.md)**: Details on user activity states.
- **[Media Library](./MEDIA_API_GUIDE.md)**: Details on file statistics.
- **[Documentation Index](./README.md)**: Return to main menu.
