# Roles & Permissions Guide

This guide covers the Role-Based Access Control (RBAC) system used to secure the application.

> [!NOTE]
> RBAC is managed by the `Acl` module and is built upon the `spatie/laravel-permission` package.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefixes**: `/roles` and `/permissions`
- **Models**: Standardized with `SoftDeletes` and `HasActiveStatus`.

---

## 🛡️ Roles Management

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/roles` | List roles. Supports `with_permissions=1`. | Yes |
| `POST` | `/roles` | Create a new system role. | Yes |
| `GET` | `/roles/{id}` | Get role details and its permissions. | Yes |
| `PUT/PATCH` | `/roles/{id}` | Update role name or status. | Yes |
| `DELETE` | `/roles/{id}` | Soft delete a role. | Yes |
| `POST` | `/roles/{id}/sync-permissions` | **Replace** all permissions for this role. | Yes |
| `POST` | `/roles/{id}/give-permissions` | **Add** permissions (additive). | Yes |
| `POST` | `/roles/{id}/revoke-permissions` | **Remove** specific permissions. | Yes |

---

## 🔑 Permissions Management

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/permissions` | List all available permissions. | Yes |
| `POST` | `/permissions` | Create a new permission node. | Yes |
| `GET` | `/permissions/{id}` | Get permission details. | Yes |
| `PATCH` | `/permissions/{id}/toggle-status` | Toggle active status. | Yes |
| `POST` | `/permissions/{id}/sync-roles` | **Replace** roles for this permission. | Yes |

---

## 🔄 Syncing Examples

### Syncing Permissions to a Role
Use `sync-permissions` to define the total set of what a role can do.

**Endpoint**: `POST /api/v1/roles/{id}/sync-permissions`  
**Payload**:
```json
{
    "permissions": ["view_users", "create_users"]
}
```

> [!IMPORTANT]
> The `sync` operation will remove any existing permissions that are not present in the new array. For additive changes, use `give-permissions`.

---

## 📚 Related Guides
- **[User Management](./USER_GUIDE.md)**: Assigning roles to specific users.
- **[Documentation Index](./README.md)**: Return to main menu.
