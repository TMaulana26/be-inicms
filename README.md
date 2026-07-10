# INI CMS Backend (API)

INI CMS Backend is a headless CMS API built with Laravel 11. It provides authentication, RBAC, media management, modular feature guides, and system statistics for a separate frontend application.

## Built With

- Laravel 11
- Laravel Sanctum
- Spatie Permission
- Spatie Media Library
- Scramble

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and NPM
- MySQL, PostgreSQL, or SQLite

## Local Setup

1. Clone the repository.
   ```bash
   git clone <repository-url>
   cd be-inicms
   ```

2. Install PHP dependencies.
   ```bash
   composer install
   ```

3. Prepare the environment.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Make sure your database credentials are correct. For local email verification, set `QUEUE_CONNECTION=sync` and configure SMTP credentials. Set `FRONTEND_URL` to your frontend application.

4. Run migrations and seeders.
   ```bash
   php artisan migrate --seed
   ```

5. Start the development server.
   ```bash
   php artisan serve
   ```

## Documentation

The docs in [docs/](docs) are the source of truth for feature behavior and API flows.

- [Documentation Hub](docs/README.md)
- [API Development Guide](docs/API_DEVELOPMENT_GUIDE.md)
- [Authentication & MFA Guide](docs/AUTH_GUIDE.md)
- [RBAC Guide](docs/ROLE_PERMISSION_GUIDE.md)
- [Media Library Guide](docs/MEDIA_API_GUIDE.md)
- [Dashboard Stats Guide](docs/STATS_GUIDE.md)
- [User Management Guide](docs/USER_GUIDE.md)
- [Blog Module Guide](docs/BLOG_GUIDE.md)
- [Page Module Guide](docs/PAGE_GUIDE.md)
- [Menu Guide](docs/MENU_GUIDE.md)
- [Settings Guide](docs/SETTING_GUIDE.md)
- [Contact Module Guide](docs/CONTACT_GUIDE.md)
- [Indexing & Queries Guide](docs/INDEX_QUERY_GUIDE.md)
- [Localization Guide](docs/LOCALIZATION_GUIDE.md)

If Scramble is enabled, the interactive API docs are available at `/docs/api` once the app is running.

## API Overview

All API endpoints are prefixed with `/api/v1` and expect `Accept: application/json`. Most endpoints require `Authorization: Bearer <token>`, except public auth endpoints like login and register.

### Authentication

The auth flow is token-based via Sanctum. Email verification and 2FA are handled in the Auth module.

- `POST /api/v1/register`
- `POST /api/v1/login`
- `POST /api/v1/logout`
- `GET /api/v1/me`
- `POST /api/v1/forgot-password`
- `POST /api/v1/reset-password`
- `POST /api/v1/email/verify`
- `POST /api/v1/email/verification-notification`
- `POST /api/v1/2fa/enable`
- `POST /api/v1/2fa/confirm`
- `DELETE /api/v1/2fa/disable`
- `POST /api/v1/2fa/challenge`

### RBAC

Roles, permissions, and users are managed through API resources plus bulk and relationship endpoints.

- Users: `/api/v1/users`
- Roles: `/api/v1/roles`
- Permissions: `/api/v1/permissions`

### Media

Media uploads and lifecycle management are exposed through the Media module.

- `GET /api/v1/media`
- `POST /api/v1/media`
- `PATCH /api/v1/media/{id}/restore`
- `PATCH /api/v1/media/{id}/toggle-status`
- `DELETE /api/v1/media/{id}/force`

### Stats

- `GET /api/v1/stats`

## Notes

- New features should be added inside a module when possible.
- The root `app/` directory is reserved for shared code used across modules.
- For exact request and response shapes, use the corresponding guide in [docs/](docs).

*Created and maintained by the INI CMS Development Team.*
