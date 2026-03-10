# Frontend Integration - Menus

This guide covers how to manage navigation menus and their items.

## Menu Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/menus` | List menus (includes `items.children` by default). |
| `POST` | `/menus` | Create a new menu. |
| `GET` | `/menus/{id}` | Get menu and its structure. |
| `PUT/PATCH` | `/menus/{id}` | Update menu properties. |
| `DELETE` | `/menus/{id}` | Delete menu. |

---

## Menu Item Endpoints

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/menu-items` | List menu items. |
| `POST` | `/menu-items` | Create a menu item. |
| `PUT/PATCH` | `/menu-items/{id}` | Update item (url, icon, order, parent_id). |
| `DELETE` | `/menu-items/{id}` | Delete item. |

---

## Nested Structure
When you fetch a menu via `GET /menus/{id}`, it returns a nested tree structure.
- **Top-level**: Items with `parent_id` = null.
- **Children**: Nesting is available via the `children` key in each item.

## Menu Management (Frontend)
When building a menu builder UI:
1. Use `GET /menus/{id}` to get the initial tree.
2. Use `POST /menu-items` to add new nodes.
3. Use `PATCH /menu-items/{id}` to change `order` or `parent_id` (for drag-and-drop).

---

## Bulk Operations
Both Menus and Menu Items support standard bulk endpoints:
- `POST .../bulk-destroy`
- `PATCH .../bulk-restore`
- `PATCH .../bulk-toggle-status`
- `POST .../bulk-force-delete`

## Filtering and Searching
- **Menu Search columns**: `name`, `slug`.
- **Menu Item Search columns**: `title`, `url`.
- **Menu Item Filters**: `menu_id`, `parent_id`.
