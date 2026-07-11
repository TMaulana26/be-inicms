# INI CMS Backend (API)

INI CMS Backend is a modern, modular, and extensible headless CMS API built with Laravel 11. It provides a robust backend content management system, authentication system, and media library for a separate frontend application.

## Key Features

- **Modular Architecture**: Features are organized into discrete, maintainable domain modules (e.g., Blog, Auth, Acl, Setting, Menu, Media, Contact, Portfolio, Skill) using `nwidart/laravel-modules`.
- **Authentication & Security**: Secure, token-based authentication via Laravel Sanctum, integrated with Two-Factor Authentication (2FA), secure password resets, and email verification.
- **Role-Based Access Control (RBAC)**: Fine-grained access control (Users, Roles, Permissions) powered by Spatie Laravel Permission.
- **Content Management**: Built-in modules for managing static pages, blogging (categories and posts), and recursive navigation menus.
- **Media Library**: Dynamic file uploads, conversions, and optimized image processing using Spatie Laravel Medialibrary.
- **Global Settings & Stats**: Application-wide key-value configuration and real-time database dashboard reporting counts.
- **Consumer Messages**: Storing and managing visitor contact requests with status and read/unread tracking.
- **Portfolio & Skills**: Modules for managing projects (screenshots, tech stacks, repository and demo URLs) and technical skills (Frontend, Backend, Tools) for badges display.

## Built With

- **Framework**: Laravel 11
- **Authentication**: Laravel Sanctum & Laravel Fortify
- **RBAC**: Spatie Laravel Permission
- **Media**: Spatie Laravel Medialibrary
- **Documentation**: Scramble (OpenAPI generation)

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and NPM
- Database (MySQL, PostgreSQL, or SQLite)

## Local Setup

1. **Clone the repository**:
   ```bash
   git clone <repository-url>
   cd be-inicms
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Prepare configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: Update your `.env` file with your database connection details and set `FRONTEND_URL` to match your frontend application URL.*

4. **Run migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

5. **Start the development server**:
   ```bash
   php artisan serve
   ```

## Documentation

The detailed source of truth for the codebase, architecture, and specific API request/response specifications is located in the [docs/](docs) directory.

For comprehensive details on each module, please visit the **[Documentation Hub (docs/README.md)](docs/README.md)**.
