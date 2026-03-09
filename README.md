# INI CMS Backend (API)

A modern, headless Content Management System backend built with Laravel 11. This repository serves as the core API, providing robust authentication, granular role-based access control (RBAC), media management, and essential CMS features.

## 🚀 Built With

- **[Laravel 11](https://laravel.com/)** - The PHP Framework for Web Artisans
- **[Laravel Sanctum](https://laravel.com/docs/sanctum)** - Featherweight API Authentication
- **[Spatie Permission](https://spatie.be/docs/laravel-permission)** - Granular Role & Permission Management
- **[Spatie Media Library](https://spatie.be/docs/laravel-medialibrary)** - Polymorphic File & Media Management
- **[Scramble](https://scramble.dedoc.co/)** - Modern OpenAPI (Swagger) Documentation Generation

## ⚙️ Prerequisites

Before you begin, ensure you have the following installed matching these requirements:

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL, PostgreSQL, or SQLite

## 🛠️ Local Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd be-inicms
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up Environment Variables**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   > **Important Configuration:** Ensure your database credentials in `.env` are correct. For email verification to work locally, set `QUEUE_CONNECTION=sync` and configure your SMTP credentials (e.g. Google App Passwords). Set `FRONTEND_URL` to point to your local frontend application.

4. **Run Migrations and Seeders**
   ```bash
   php artisan migrate --seed
   ```
   *(Seeders will create initial roles, permissions, and a default super-admin account.)*

5. **Start the Development Server**
   ```bash
   php artisan serve
   ```

## 📚 API Documentation

This project utilizes **Scramble** to automatically generate comprehensive OpenAPI documentation.

Once your local server is running, you can access the interactive Swagger UI here:
👉 `http://localhost:8000/docs/api`

*The API documentation is always synchronized with the current codebase, meaning you'll see exact request bodies, query parameters, headers, and response formats instantly.*

## 🔒 Authentication Flow

This platform uses a purely API-driven (Headless) approach to authentication via Laravel Sanctum. No traditional browser sessions or blade views are utilized.

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/login` | Login with email & password |
| `POST` | `/api/register` | Register a new user account |
| `POST` | `/api/forgot-password` | Request a password reset email |
| `POST` | `/api/reset-password` | Reset a password using a token |
| `POST` | `/api/email/verify` | Verify an email address using signed URL params |

### 1. Standard Login with "Remember Me"
- Endpoint: `POST /api/login`
- Provide your email and password.
- Optionally include `"remember": true` to receive a token valid for **1 Year** instead of the standard 60 minutes.

### 2. Email Verification
Users must verify their email addresses before accessing protected resources.
- Registration automatically sends a verification email.
- The link in the email is a cryptographically signed URL pointing to the `FRONTEND_URL`.
- The frontend extracts the parameters (`id`, `hash`, `expires`, `signature`) from the URL and sends them via `POST /api/email/verify` to officially verify the account.
- Authenticated users can request a new verification email via `POST /api/email/verification-notification`.

### 3. Two-Factor Authentication (2FA)
Verified users can opt in to 2FA for maximum security.
- **Enable:** `POST /api/2fa/enable` — returns a QR Code SVG and Recovery Codes.
- **Confirm:** `POST /api/2fa/confirm` — validates the first TOTP code to activate 2FA.
- **Disable:** `DELETE /api/2fa/disable` — deactivates 2FA for the user.
- **The Challenge:** If 2FA is active, a login attempt via `POST /api/login` returns a `requires_2fa: true` flag along with a temporary 10-minute token restricted to the `['2fa']` ability only.
- **Completion:** Supply this temporary token to `POST /api/2fa/challenge` along with your Authenticator code to receive a full-access token.

## 👥 Role-Based Access Control (RBAC)

The application employs Spatie's Permission package configured over an API. We rely on `name` instead of `id` for role/permission assignments, making payloads robust against dynamic environments.

### Users

Standard CRUD via `apiResource` plus extended endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `PATCH` | `/api/users/{user}/toggle-status` | Toggle a user's active status |
| `PATCH` | `/api/users/{id}/restore` | Restore a soft-deleted user |
| `DELETE`| `/api/users/{id}/force-delete` | Permanently delete a user |
| `PATCH` | `/api/users/bulk-toggle-status` | Bulk toggle active status |
| `POST`  | `/api/users/bulk-destroy` | Bulk soft-delete users |
| `PATCH` | `/api/users/bulk-restore` | Bulk restore soft-deleted users |
| `POST`  | `/api/users/bulk-force-delete` | Bulk permanently delete users |
| `POST`  | `/api/users/{user}/sync-roles` | Replace all roles for a user |
| `POST`  | `/api/users/{user}/assign-roles` | Assign roles to a user |
| `POST`  | `/api/users/{user}/remove-roles` | Remove roles from a user |

### Roles

Standard CRUD via `apiResource` plus extended endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `PATCH` | `/api/roles/{role}/toggle-status` | Toggle a role's active status |
| `PATCH` | `/api/roles/{id}/restore` | Restore a soft-deleted role |
| `DELETE`| `/api/roles/{id}/force-delete` | Permanently delete a role |
| `PATCH` | `/api/roles/bulk-toggle-status` | Bulk toggle active status |
| `POST`  | `/api/roles/bulk-destroy` | Bulk soft-delete roles |
| `PATCH` | `/api/roles/bulk-restore` | Bulk restore soft-deleted roles |
| `POST`  | `/api/roles/bulk-force-delete` | Bulk permanently delete roles |
| `POST`  | `/api/roles/{role}/sync-permissions` | Replace all permissions for a role |
| `POST`  | `/api/roles/{role}/give-permissions` | Assign permissions to a role |
| `POST`  | `/api/roles/{role}/revoke-permissions` | Revoke permissions from a role |

### Permissions

Standard CRUD via `apiResource` plus extended endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `PATCH` | `/api/permissions/{permission}/toggle-status` | Toggle active status |
| `PATCH` | `/api/permissions/{id}/restore` | Restore a soft-deleted permission |
| `DELETE`| `/api/permissions/{id}/force-delete` | Permanently delete a permission |
| `PATCH` | `/api/permissions/bulk-toggle-status` | Bulk toggle active status |
| `POST`  | `/api/permissions/bulk-destroy` | Bulk soft-delete permissions |
| `PATCH` | `/api/permissions/bulk-restore` | Bulk restore soft-deleted permissions |
| `POST`  | `/api/permissions/bulk-force-delete` | Bulk permanently delete permissions |
| `POST`  | `/api/permissions/{permission}/sync-roles` | Replace all roles for a permission |
| `POST`  | `/api/permissions/{permission}/assign-roles` | Assign roles to a permission |
| `POST`  | `/api/permissions/{permission}/remove-roles` | Remove roles from a permission |

### Response Conventions
Endpoints involving bulk operations (e.g., toggling the active status of multiple users) heavily utilize a standard bulk response trait to communicate exactly which operations succeeded and which specifically failed.

## 🖼️ Headless Media & Asset Management

A centralized "Smart Local Storage" system powered by `spatie/laravel-medialibrary` handles all asset uploads.

- **Unified Upload Endpoint**: `POST /api/media` accepts standard `multipart/form-data` file uploads.
- **Auto-Optimization**: Uploaded images are instantly intercepted, converted into `.webp` format using `spatie/image`, and compressed to save disk space.
- **Responsive Variants**: Along with the original file, the system automatically generates lightweight `thumbnail` (200px) and `preview` (800px) variants.
- **Polymorphic Storage**: Every media item generates a unique `id` which can be attached polymorphically to any model (Avatars, Blog Posts, Product Images, etc.).
- **Full Lifecycle Management**: Soft-delete, restore, and permanent deletion are all supported.

### Media Endpoints

Standard CRUD via `apiResource` plus extended endpoints:

| Method | Endpoint | Description |
|--------|----------|-------------|
| `PATCH` | `/api/media/{media}/toggle-status` | Toggle a media item's active status |
| `PATCH` | `/api/media/{id}/restore` | Restore a soft-deleted media item |
| `DELETE`| `/api/media/{id}/force` | Permanently delete a media item |
| `PATCH` | `/api/media/bulk-toggle-status` | Bulk toggle active status |
| `POST`  | `/api/media/bulk-destroy` | Bulk soft-delete media items |
| `PATCH` | `/api/media/bulk-restore` | Bulk restore soft-deleted media items |
| `POST`  | `/api/media/bulk-force-delete` | Bulk permanently delete media items |

## 📊 Stats

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/stats` | Retrieve application-wide statistics |

---
*Created and maintained by the INI CMS Development Team.*
