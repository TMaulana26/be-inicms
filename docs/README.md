# Project Documentation Hub

Welcome to the **be-inicms** API and Development documentation. This directory contains all the information needed to develop for and integrate with the INI CMS backend.

## 🚀 Getting Started

If you are a new developer on the project, please start with the **API Development Guide**.

- **[API Development Guide](./API_DEVELOPMENT_GUIDE.md)**: Standardized modular development workflow, namespaces, and artisan commands.
- **[Indexing & Queries](./INDEX_QUERY_GUIDE.md)**: How to use the global filtering, sorting, and pagination system.

---

## 🔐 Core Modules

Documentation for the foundational security and system modules.

- **[Authentication & 2FA](./AUTH_GUIDE.md)**: Login, registration, and Two-Factor Authentication flows.
- [Blog Module Guide](./BLOG_GUIDE.md) — Manage categories (Blog & Media), posts, and common taxonomies.
- [Page Module Guide](./PAGE_GUIDE.md) — Manage static content and pages.
- [Settings Guide](./SETTING_GUIDE.md) — Global project settings management.
- **[Roles & Permissions](./ROLE_PERMISSION_GUIDE.md)**: RBAC system management (Spatie Permissions).
- **[User Management](./USER_GUIDE.md)**: CRUD operations for system users and role assignments.

---

## 🛠️ Feature Modules

Guides for specific CMS features and resources.

- **[Media Library](./MEDIA_API_GUIDE.md)**: Handling file uploads, conversions, and bulk media management.
- **[Navigation Menus](./MENU_GUIDE.md)**: Managing recursive, self-referencing menu structures.
- **[System Settings](./SETTING_GUIDE.md)**: Application-wide configuration and grouped settings.
- **[Dashboard Stats](./STATS_GUIDE.md)**: Retrieval of system-wide reporting counts.

---

> [!TIP]
> All API endpoints are prefixed with `/api/v1` and require an `Accept: application/json` header. Most endpoints (except Login/Register) also require a valid `Authorization: Bearer <token>` header.
