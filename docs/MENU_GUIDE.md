# Navigation Menus Guide

This guide covers how to manage dynamic navigation structures using our recursive menu system.

> [!NOTE]
> Menus are handled by the `Menu` module. It uses a self-referencing table structure where "Menus" are root nodes and "Menu Items" are children linked via `parent_id`.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Prefix**: `/menus`
- **Key Features**: Recursive tree loading, automatic slug generation, and order management.

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

## 🌳 Recursive Tree Structure

When you fetch a menu, it returns a nested structure using the `children` key.

- **Root Menu**: `parent_id` is `null`.
- **Menu Item**: `parent_id` refers to a parent node.

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
- **[System Settings](./SETTING_GUIDE.md)**: Configuring menu location constants.
- **[Documentation Index](./README.md)**: Return to main menu.
