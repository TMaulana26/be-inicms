# INI CMS Backend (API)

A modern, headless Content Management System backend built with Laravel 11. This repository serves as the core API, providing robust authentication, granular role-based access control (RBAC), and essential CMS features.

## 🚀 Built With

- **[Laravel 11](https://laravel.com/)** - The PHP Framework for Web Artisans
- **[Laravel Sanctum](https://laravel.com/docs/sanctum)** - Featherweight API Authentication
- **[Spatie Permission](https://spatie.be/docs/laravel-permission)** - Granular Role & Permission Management
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
   *(Assuming seeders exist for initial roles and an admin account)*

5. **Start the Development Server**
   ```bash
   npm install && npm run dev
   ```
   Alternatively, you can manually start the server:
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

### 1. Standard Login with "Remember Me"
- Endpoint: `POST /api/login`
- Provides your email and password.
- Optionally include `"remember": true` to receive a token valid for **1 Year** instead of the standard 60 minutes.

### 2. Email Verification
Users must verify their email addresses.
- Registration will automatically send an email to the user.
- The link in the email is a cryptographically signed URL pointing to the `FRONTEND_URL`.
- The frontend will extract the parameters (`id`, `hash`, `expires`, `signature`) from the URL and transmit them to `GET /api/email/verify/{id}/{hash}` to officially verify the account.

### 3. Two-Factor Authentication (2FA)
Verified users can opt in to 2FA for maximum security.
- **Enable:** `POST /api/2fa/enable` (returns QR Code SVG and Recovery Codes).
- **Confirm:** `POST /api/2fa/confirm` (validates the first code to activate).
- **The Challenge:** If 2FA is active, an attempt to log in using `POST /api/login` will return a `requires_2fa: true` flag along with a temporary 10-minute token restricted absolutely to the `['2fa']` ability.
- **Completion:** Supply this temporary token to `POST /api/login/2fa-challenge` along with your Authenticator code to unlock a full-access token.

## 👥 Role-Based Access Control (RBAC)

The application employs Spatie's Permission package configured over an API.
- We rely on `name` instead of `id` for role and permission assignments, making payload definitions robust against dynamic environments.
- Granular endpoints are available to **assign**, **remove**, and fully **sync** roles to users.

### Response Conventions
Endpoints involving bulk operations (e.g., toggling the active status of multiple users) heavily utilize a standard bulk response trait to communicate exactly which operations succeeded and which specifically failed.

---
*Created and maintained by the INI CMS Development Team.*
