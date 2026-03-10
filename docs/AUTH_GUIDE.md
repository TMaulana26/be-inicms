# Frontend Integration - Authentication & 2FA

This guide details the authentication and Two-Factor Authentication (2FA) flows for the INI CMS API.

## Base Configuration

- **Base URL**: `/api/v1`
- **Authentication Strategy**: Laravel Sanctum (Stateful for Web, Token-based for Mobile/SPA).
- **Headers**: 
    - `Accept: application/json`
    - `Authorization: Bearer <your_token>`

---

## 1. Core Authentication

### Registration
**Endpoint**: `POST /login` (Note: Actually `POST /register` based on routes)  
**Endpoint**: `POST /register`

| Field | Type | Description |
|---|---|---|
| `name` | string | Full name of the user. |
| `email` | string | Unique email address. |
| `password` | string | Minimum 8 characters. |
| `password_confirmation` | string | Must match password. |

### Login
**Endpoint**: `POST /login`

| Field | Type | Description |
|---|---|---|
| `email` | string | User email. |
| `password` | string | User password. |

**Response (Success)**:
```json
{
    "success": true,
    "message": "Login successful.",
    "data": {
        "token": "1|abc123xyz...",
        "user": { ... }
    }
}
```

**Response (2FA Required)**:
If 2FA is enabled, the login will return a temporary token with only the `2fa` ability.
```json
{
    "success": true,
    "message": "2FA challenge required.",
    "data": {
        "token": "2|temp_2fa_token...",
        "requires_2fa": true
    }
}
```

---

## 2. Two-Factor Authentication (2FA)

### Enable 2FA
**Endpoint**: `POST /2fa/enable` (Requires auth)  
Returns the SVG QR code and Secret Key.

### Confirm 2FA
**Endpoint**: `POST /2fa/confirm`  
Validates the code from the authenticator app to finalize activation.

| Field | Type | Description |
|---|---|---|
| `code` | string | 6-digit code from App. |

### Disable 2FA
**Endpoint**: `DELETE /2fa/disable`  
Removes 2FA from the account.

### 2FA Challenge (During Login)
**Endpoint**: `POST /2fa/challenge`  
Used when login returns `requires_2fa: true`. Use the temporary token in the `Authorization` header.

| Field | Type | Description |
|---|---|---|
| `code` | string | 6-digit code from App. |

**Success**: Returns the final access token with full permissions.

---

## 3. Account Management

### Get Current User
**Endpoint**: `GET /me`

### Logout
**Endpoint**: `POST /logout`

### Password Recovery
- `POST /forgot-password`: Send reset link to email.
- `POST /reset-password`: Update password using token from email.

### Email Verification
- `POST /email/verify`: Verify email with signed URL parameters.
- `POST /email/verification-notification`: Resend verification email.
