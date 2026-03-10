# Frontend Integration - Roles & Permissions

This guide covers the Role-Based Access Control (RBAC) system.

## Roles Management

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/roles` | List roles. |
| `POST` | `/roles` | Create a new role. |
| `GET` | `/roles/{id}` | Get role details. |
| `PUT/PATCH` | `/roles/{id}` | Update role. |
| `DELETE` | `/roles/{id}` | Delete role. |
| `POST` | `/roles/{id}/sync-permissions` | Sync permissions to role. |

---

## Permissions Management

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/permissions` | List permissions. |
| `POST` | `/permissions` | Create a permission. |
| `GET` | `/permissions/{id}` | Get details. |
| `PUT/PATCH` | `/permissions/{id}` | Update. |
| `DELETE` | `/permissions/{id}` | Delete. |

---

## Syncing Permissions to Roles

To specify what a role can do, use the `sync-permissions` endpoint.

**Endpoint**: `POST /roles/{id}/sync-permissions`  
**Payload**:
```json
{
    "permissions": ["view_users", "create_users", "delete_users"]
}
```

---

## Bulk Operations
Both Roles and Permissions support standard bulk endpoints:
- `POST .../bulk-destroy`
- `PATCH .../bulk-restore`
- `PATCH .../bulk-toggle-status`
- `POST .../bulk-force-delete`

## Filtering and Searching
- **Search columns (Roles)**: `name`, `guard_name`.
- **Search columns (Permissions)**: `name`, `guard_name`.
