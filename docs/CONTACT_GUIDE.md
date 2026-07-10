# Contact Module Guide

The **Contact** module handles consumer contact submissions, inquiries, and messages sent from the frontend website.

## Features

- **Public Submission**: Website visitors can submit messages with their name, email, and message content.
- **Read/Unread Tracking**: Messages can be toggled as read/unread to help administrators manage incoming inquiries.
- **Advanced Indexing**: Search through messages by name, email, or message text, and filter by read status or active status.
- **Standard Routing**: Integrates with the `api/v1` prefix.
- **Soft Deletes**: Supports soft deletion, restoration, and permanent force deletion.
- **Bulk Actions**: Perform bulk delete, restore, force delete, active status toggle, and read status toggle on multiple messages in one request.

---

## API Endpoints

### Public Endpoints

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/contact-messages` | Submit a new contact message | Guest (Public) |

### Protected Endpoints (Requires Sanctum Token)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/v1/contact-messages` | List all contact messages (filtered, sorted, paginated) |
| `GET` | `/api/v1/contact-messages/{id}` | Show details of a specific contact message |
| `DELETE` | `/api/v1/contact-messages/{id}` | Soft delete a contact message |
| `PATCH` | `/api/v1/contact-messages/{id}/restore` | Restore a soft-deleted contact message |
| `DELETE` | `/api/v1/contact-messages/{id}/force-delete` | Permanently delete a contact message |
| `PATCH` | `/api/v1/contact-messages/{id}/toggle-status` | Toggle active/inactive status of a message |
| `PATCH` | `/api/v1/contact-messages/{id}/toggle-read` | Toggle read/unread status of a message |
| `POST` | `/api/v1/contact-messages/bulk/delete` | Bulk soft delete messages |
| `PATCH` | `/api/v1/contact-messages/bulk/restore` | Bulk restore soft-deleted messages |
| `PATCH` | `/api/v1/contact-messages/bulk/toggle-status` | Bulk toggle active status of messages |
| `PATCH` | `/api/v1/contact-messages/bulk/toggle-read` | Bulk toggle read status of messages |
| `POST` | `/api/v1/contact-messages/bulk/force-delete` | Bulk permanently delete messages |

---

## Usage Examples

### Submit a Contact Message (Public)

**Request:**
`POST /api/v1/contact-messages`

**Payload:**
```json
{
    "name": "Jane Doe",
    "email": "jane.doe@example.com",
    "message": "Hi, I would like to inquire about your services."
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Contact message sent successfully.",
    "data": {
        "id": 1,
        "name": "Jane Doe",
        "email": "jane.doe@example.com",
        "message": "Hi, I would like to inquire about your services.",
        "is_read": false,
        "is_active": true,
        "created_at": "2026-07-10T13:30:00Z",
        "updated_at": "2026-07-10T13:30:00Z",
        "deleted_at": null
    }
}
```

---

## 📚 Related Guides
- **[API Development Guide](./API_DEVELOPMENT_GUIDE.md)**: Standardized modular development.
- **[Indexing & Queries](./INDEX_QUERY_GUIDE.md)**: How to filter and search contact messages.
- **[Documentation Index](./README.md)**: Return to main menu.
