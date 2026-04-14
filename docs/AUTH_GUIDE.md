# Authentication & MFA Guide

This guide details the authentication and Multi-Factor Authentication (MFA/2FA) flows for the INI CMS API.

> [!NOTE]
> All authentication logic is contained within the `Auth` module. It uses Laravel Sanctum for secure, token-based authentication.

---

## 🏗️ Technical Overview

- **Base URL**: `/api/v1`
- **Strategy**: Bearer Token (Sanctum)
- **Required Headers**:
    - `Accept: application/json`
    - `Authorization: Bearer {token}`

---

## 🔐 Core Authentication

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/register` | Register a new user account. | No |
| `POST` | `/login` | Authenticate and retrieve an access token. | No |
| `POST` | `/logout` | Revoke the current access token. | Yes |
| `GET` | `/me` | Retrieve the authenticated user's profile. | Yes |

### Login Response (Success)
```json
{
    "success": true,
    "message": "User logged in successfully.",
    "data": {
        "user": { "id": 1, "name": "Admin", ... },
        "access_token": "1|abc123xyz...",
        "token_type": "Bearer",
        "expires_at": "2026-04-14 12:00:00"
    }
}
```

---

## 🛡️ Two-Factor Authentication (2FA)

If a user has 2FA enabled, the initial `/login` will return a `requires_2fa: true` flag and a temporary token.

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/2fa/enable` | Generate QR code and secret for 2FA setup. | Yes |
| `POST` | `/2fa/confirm` | Confirm setup with a code from the app. | Yes |
| `DELETE` | `/2fa/disable` | Disable 2FA for the account. | Yes |
| `POST` | `/2fa/challenge` | Complete login using a 2FA or recovery code. | Yes (2FA) |

> [!IMPORTANT]
> The `/2fa/challenge` endpoint requires the temporary token returned during the login phase. Once verified, it returns a full access token.

---

## 🔑 Password & Verification

| Method | Endpoint | Description | Auth |
| :--- | :--- | :--- | :--- |
| `POST` | `/forgot-password` | Request a password reset link. | No |
| `POST` | `/reset-password` | Reset password using email token. | No |
| `POST` | `/email/verify` | Verify email with signed URL. | Yes |
| `POST` | `/email/verification-notification` | Resend verification email. | Yes |

---

## 📚 Related Guides
- **[User Management](./USER_GUIDE.md)**: Managing user accounts and roles.
- **[Documentation Index](./README.md)**: Return to main menu.
