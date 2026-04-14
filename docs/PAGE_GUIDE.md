# Page Module Guide

The **Page** module handles static content like "About Us", "Privacy Policy", or custom landing pages.

## Features

- **Static Content**: Manage standalone pages with rich text content.
- **Multimedia Support**: Integration with Spatie MediaLibrary for page images/banners.
- **Author Tracking**: All pages are automatically associated with the creating user.
- **Standard Routing**: Follows the `api/v1` standardized prefix.
- **Bulk Operations**: Bulk delete, restore, and force delete support.

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/pages` | List all pages (filtered/paginated) |
| POST | `/api/v1/pages` | Create a new page |
| GET | `/api/v1/pages/{id}` | Show page details |
| PUT | `/api/v1/pages/{id}` | Update page |
| DELETE | `/api/v1/pages/{id}` | Soft delete page |
| POST | `/api/v1/pages/{id}/restore` | Restore soft-deleted page |
| DELETE | `/api/v1/pages/{id}/force` | Permanent deletion |

## Usage Examples

> [!IMPORTANT]
> When creating a page, the `status` field must be either `draft` or `published`.

### Creating a Page with Image

Use `multipart/form-data` to submit the `page_image` file along with the page content.
