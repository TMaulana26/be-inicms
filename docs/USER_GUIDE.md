# Frontend Integration - User Management

This guide covers managing users through the API.

## Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/users` | List users (supports search, filters, pagination). |
| `POST` | `/users` | Create a new user. |
| `GET` | `/users/{id}` | Get user details. |
| `PUT/PATCH` | `/users/{id}` | Update user information. |
| `DELETE` | `/users/{id}` | Soft delete a user. |
| `PATCH` | `/users/{id}/toggle-status` | Toggle user activity status. |
| `PATCH` | `/users/{id}/restore` | Restore a soft-deleted user. |
| `DELETE` | `/users/{id}/force-delete` | Permanently delete a user. |

---

## Role Assignment

You can manage user roles through dedicated endpoints:

- `POST /users/{id}/sync-roles`: Replaces all current roles with the provided list.
- `POST /users/{id}/assign-roles`: Adds roles to the user without removing existing ones.
- `POST /users/{id}/remove-roles`: Removes specific roles.

**Request Body Example**:
```json
{
    "roles": ["admin", "editor"]
}
```

---

## Bulk Operations

Perform actions on multiple users at once.

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/users/bulk-destroy` | Bulk soft-delete. |
| `PATCH` | `/users/bulk-restore` | Bulk restore. |
| `PATCH` | `/users/bulk-toggle-status` | Bulk status toggle. |
| `POST` | `/users/bulk-force-delete` | Bulk permanent delete. |

**Payload**:
```json
{
    "ids": [10, 11, 12]
}
```

---

## Filtering and Searching
Refer to the [Index Query Guide](./INDEX_QUERY_GUIDE.md) for standard parameters like `search`, `status`, `trashed`, and `per_page`.
- **Search columns**: `name`, `email`.
