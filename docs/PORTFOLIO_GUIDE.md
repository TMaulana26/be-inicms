# Portfolio Module Guide

This guide details the Portfolio Project module in the INI CMS API. It allows you to manage showcases of web projects, stack info, repository/demo URLs, and upload screenshots.

---

## 🏗️ Technical Overview

- **Namespace**: `Modules\Portfolio`
- **Database Table**: `portfolio_projects`
- **Prefix**: `/api/v1`
- **Image Collection**: `screenshot` (handled via Spatie Media Library)

---

## 🔑 Endpoint API Reference

| Method | Endpoint | Description | Auth Required |
| :--- | :--- | :--- | :--- |
| `GET` | `/projects` | List active projects (public view). | No |
| `GET` | `/projects/{id}` | Get details of a single project. | No |
| `POST` | `/projects` | Create a new project. | Yes |
| `PUT` | `/projects/{id}` | Update project details. | Yes |
| `DELETE` | `/projects/{id}` | Soft delete a project. | Yes |
| `PATCH` | `/projects/{id}/toggle-status` | Toggle project active status. | Yes |

### Bulk and Soft Delete Endpoints (Protected)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `PATCH` | `/projects/{id}/restore` | Restore a soft-deleted project. |
| `DELETE` | `/projects/{id}/force-delete` | Permanently delete a project from database & disk. |
| `POST` | `/projects/bulk/delete` | Soft delete multiple projects. |
| `PATCH` | `/projects/bulk/restore` | Restore multiple soft-deleted projects. |
| `PATCH` | `/projects/bulk/toggle-status` | Toggle active status for multiple projects. |
| `POST` | `/projects/bulk/force-delete` | Permanently delete multiple projects. |

---

## 📦 Request Validation (Store & Update)

```json
{
    "title": {
        "en": "INI CMS - Headless Content Management System",
        "id": "INI CMS - Sistem Manajemen Konten Headless"
    },
    "slug": "ini-cms-headless-content-management-system",
    "category": "BACKEND",
    "description": {
        "en": "A robust headless CMS API built with Laravel 11.",
        "id": "API CMS headless kokoh yang dibangun dengan Laravel 11."
    },
    "tech_stack": ["Laravel 11", "Sanctum", "Fortify"],
    "github_url": "https://github.com/TMaulana26/be-inicms",
    "demo_url": "https://api.inicms.com/docs/api",
    "is_active": true,
    "screenshot": "binary_image_file"
}
```

---

## 📥 Response Payload Example

### Success Listing (Active Projects)
`GET /api/v1/projects`
```json
{
    "success": true,
    "message": "Projects retrieved successfully.",
    "data": [
        {
            "id": 1,
            "title": {
                "en": "INI CMS - Headless Content Management System",
                "id": "INI CMS - Sistem Manajemen Konten Headless"
            },
            "slug": "ini-cms-headless-content-management-system",
            "category": "BACKEND",
            "description": {
                "en": "A robust headless CMS API built with Laravel 11 supporting authentication, RBAC, media library, and modular feature guides.",
                "id": "API CMS headless kokoh yang dibangun dengan Laravel 11 mendukung autentikasi, RBAC, media library, dan panduan fitur modular."
            },
            "tech_stack": [
                "Laravel 11",
                "Sanctum",
                "Fortify",
                "Spatie Permission",
                "Spatie Media Library",
                "Scramble"
            ],
            "github_url": "https://github.com/TMaulana26/be-inicms",
            "demo_url": "https://api.inicms.com/docs/api",
            "screenshot_url": "http://localhost:8000/storage/media/1/screenshot.png",
            "is_active": true,
            "created_at": "2026-07-10T22:07:11.000000Z",
            "updated_at": "2026-07-10T22:07:11.000000Z",
            "deleted_at": null
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/v1/projects?page=1",
        "last": "http://localhost:8000/api/v1/projects?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://localhost:8000/api/v1/projects?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://localhost:8000/api/v1/projects",
        "per_page": 15,
        "to": 2,
        "total": 2
    }
}
```

---

## 📚 Related Guides
- **[Documentation Index](./README.md)**: Return to main menu.
- **[Media Library Guide](./MEDIA_API_GUIDE.md)**: Learn more about media file uploads.
