# User Management Guide

This guide covers the management of system users, including account creation, status toggling, and role assignments.

> [!NOTE]
> User management is part of the `Acl` (Access Control List) module. It handles both core user data and integration with the RBAC system.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/users`
- **Security**: All endpoints except basic registration require administrative authentication.

---

## 👥 User Endpoints

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/users` | List users with search and role filtering. | Yes |
| `POST` | `/users` | Create a new administrative or system user. | Yes |
| `GET` | `/users/{id}` | Get full user profile, roles, and permissions. | Yes |
| `PUT/PATCH` | `/users/{id}` | Update account details (name, email, password). | Yes |
| `PATCH` | `/users/{id}/toggle-status`| Toggle `is_active` status. | Yes |
| `PATCH` | `/users/{id}/restore` | Restore a soft-deleted user. | Yes |
| `DELETE` | `/users/{id}` | Soft delete a user account. | Yes |
| `DELETE` | `/users/{id}/force-delete` | Permanent account deletion. | Yes |

---

## 🎭 Role Assignment

Users can be assigned multiple roles (e.g., "Editor", "Admin") through these dedicated endpoints:

- **Sync Roles**: `POST /api/v1/users/{id}/sync-roles` (Replaces all current roles)
- **Assign Roles**: `POST /api/v1/users/{id}/assign-roles` (Additive)
- **Remove Roles**: `POST /api/v1/users/{id}/remove-roles` (Subtractive)

### Payload Example
```json
{
    "roles": ["admin", "editor"]
}
```

---

## 📦 Bulk Operations

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/users/bulk-destroy` | Bulk soft-delete accounts. |
| `PATCH` | `/users/bulk-restore` | Bulk restore accounts. |
| `PATCH` | `/users/bulk-toggle-status` | Bulk status toggle. |
| `POST` | `/users/bulk-force-delete` | Permanent bulk deletion. |

---

## 📚 Related Guides
- **[Roles & Permissions](./ROLE_PERMISSION_GUIDE.md)**: Defining what roles are available.
- **[Authentication & 2FA](./AUTH_GUIDE.md)**: Login and registration flows.
- **[Documentation Index](./README.md)**: Return to main menu.
