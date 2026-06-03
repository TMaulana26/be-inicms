# Navigation Menus Guide

This guide covers how to manage dynamic navigation structures using our recursive menu system.

> [!NOTE]
> Menus are handled by the `Menu` module. It uses a self-referencing `menus` table structure. Root nodes represent "Menu Groups" (e.g., Main Navbar), and child nodes represent the actual "Menu Items" linked via `parent_id`.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/menus`
- **Key Features**: Recursive tree loading, automatic slug generation, order management, and **Localization (i18n)**.

---

## 🗺️ Menu Endpoints

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `GET` | `/menus` | List root menus (includes building the tree). | Yes |
| `GET` | `/menus?all=1` | List all nodes as a flat list. | Yes |
| `POST` | `/menus` | Create a new menu or child item. | Yes |
| `GET` | `/menus/{id}` | Get a specific menu and its entire child tree. | Yes |
| `PUT/PATCH` | `/menus/{id}` | Update node properties (url, icon, order). | Yes |
| `PATCH` | `/menus/{id}/restore` | Restore a soft-deleted menu node. | Yes |
| `PATCH` | `/menus/{id}/toggle-status` | Toggle active status. | Yes |
| `DELETE` | `/menus/{id}` | Soft delete (cascades to children). | Yes |
| `DELETE` | `/menus/{id}/force-delete` | Permanent tree deletion. | Yes |

---

## 🌳 Recursive Tree Management

### Intelligent Synchronization
When updating a menu via `PUT/PATCH /menus/{id}`, you can provide a nested `children` array. Our system performs an **intelligent sync**:
1.  **Update**: If a child item has an `id`, it is updated in place (preserving its database identity).
2.  **Create**: If a child item lacks an `id`, it is created as a new node.
3.  **Delete**: Any existing child item NOT present in the provided array is automatically soft-deleted.

This allows for deep, recursive management of entire menu branches in a single API call without breaking database references.

---

## 🌍 Localization (i18n)

The Menu module supports multiple languages.
- **Translatable Fields**: `name`, `description` (for Groups) and `title` (for Items).
- **Usage**:
    - Pass a string for the current locale: `"title": "Home"`
    - Or pass an object for multiple languages: `"title": {"en": "Home", "id": "Beranda"}`
- **API Response**: By default returns the string for the active locale. Use `?with_translations=1` to see the full JSON object.

---

### Management Best Practices

> [!TIP]
> **Auto-Generation**: If you provide a `title` during creation but omit the `name` or `slug`, the backend will automatically generate them based on the title.

1.  **Reordering**: Use `PATCH /api/v1/menus/{id}` to update the `order` property for drag-and-drop support.
2.  **Reparenting**: Change the `parent_id` to move an item to a different branch.

---

## 📦 Bulk Operations

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/menus/bulk-destroy` | Bulk soft-delete tree branches. |
| `PATCH` | `/menus/bulk-restore` | Bulk restore branches. |
| `PATCH` | `/menus/bulk-toggle-status` | Bulk toggle status for items. |

---

## 📚 Related Guides
- **[Localization Guide](./LOCALIZATION_GUIDE.md)**: Global multi-language setup.
- **[System Settings](./SETTING_GUIDE.md)**: Configuring menu location constants.
- **[Documentation Index](./README.md)**: Return to main menu.
