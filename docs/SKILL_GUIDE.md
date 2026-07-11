# Skill Module Guide

This guide details the Skill module in the INI CMS API. It allows you to manage technical skills categorized into Backend, Frontend, and Tools, sorted by displaying priority (`order`). These are rendered as badges on the portfolio page.

---

## 🏗️ Technical Overview

- **Namespace**: `Modules\Skill`
- **Database Table**: `skills`
- **Standard Prefix**: `/api/v1`
- **Flat Public Endpoint**: `/api/skills`

---

## 🔑 Endpoint API Reference

### Public Endpoints

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/skills` | List active skills sorted by `order` (for direct frontend looping). | No |

### Protected Endpoints (Admin CRUD)

All endpoints below require authentication and are prefixed with `/api/v1`.

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/skills` | List skills (paginated, supports filter and sorting). |
| `GET` | `/skills/{id}` | Get details of a single skill. |
| `POST` | `/skills` | Create a new skill. |
| `PUT` | `/skills/{id}` | Update skill details. |
| `DELETE` | `/skills/{id}` | Soft delete a skill. |
| `PATCH` | `/skills/{id}/toggle-status` | Toggle skill active status. |

### Bulk and Soft Delete Endpoints (Protected)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `PATCH` | `/skills/{id}/restore` | Restore a soft-deleted skill. |
| `DELETE` | `/skills/{id}/force-delete` | Permanently delete a skill from the database. |
| `POST` | `/skills/bulk/delete` | Soft delete multiple skills. |
| `PATCH` | `/skills/bulk/restore` | Restore multiple soft-deleted skills. |
| `PATCH` | `/skills/bulk/toggle-status` | Toggle active status for multiple skills. |
| `POST` | `/skills/bulk/force-delete` | Permanently delete multiple skills. |

---

## 📦 Request Validation (Store & Update)

```json
{
    "name": "Vue.js",
    "category": "frontend",
    "order": 1,
    "is_active": true
}
```

---

## 📥 Response Payload Example

### Success Listing (Public Badges)
`GET /api/skills`
```json
{
    "success": true,
    "message": "Skills retrieved successfully.",
    "data": [
        {
            "id": 1,
            "name": "Vue.js",
            "category": "frontend",
            "order": 1,
            "is_active": true,
            "created_at": "2026-07-11T04:36:39.000000Z",
            "updated_at": "2026-07-11T04:36:39.000000Z",
            "deleted_at": null
        },
        {
            "id": 2,
            "name": "Laravel",
            "category": "backend",
            "order": 1,
            "is_active": true,
            "created_at": "2026-07-11T04:36:39.000000Z",
            "updated_at": "2026-07-11T04:36:39.000000Z",
            "deleted_at": null
        }
    ]
}
```

---

## 📚 Related Guides
- **[Documentation Index](./README.md)**: Return to main menu.
- **[Portfolio Guide](./PORTFOLIO_GUIDE.md)**: Managing portfolio projects.
