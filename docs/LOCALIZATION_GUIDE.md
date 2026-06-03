# Localization (i18n) Guide

This guide explains how to use the multi-language support in the **be-inicms** backend.

## 🌍 Overview

The CMS uses `spatie/laravel-translatable` to handle multi-language content. Translatable fields are stored as JSON in the database and automatically handled by the models and API resources.

### Supported Locales
By default, the system supports: `en` (English) and `id` (Indonesian).

---

## 🛠️ How it Works

### 1. Locale Detection
The system automatically detects the requested language using the `LocaleMiddleware`. It follows this priority:
1.  **Query Parameter**: `?lang=id`
2.  **Header**: `Accept-Language: id`
3.  **Fallback**: Default app locale (`en`).

### 2. Translatable Models
The following models have translatable fields:

| Module | Model | Translatable Fields |
| :--- | :--- | :--- |
| **Page** | `Page` | `title`, `content` |
| **Blog** | `Post` | `title`, `summary`, `content` |
| **Blog** | `Category` | `name`, `description` |
| **Menu** | `Menu` | `name`, `description` |
| **Menu** | `MenuItem` | `title` |
| **Acl** | `Role` | `display_name` |
| **Acl** | `Permission` | `display_name` |

---

## 📡 API Usage

### Getting Content
By default, the API returns the string for the current active locale.

**Example Request**: `GET /api/v1/pages/1?lang=id`
**Response**:
```json
{
  "id": 1,
  "title": "Tentang Kami",
  "content": "..."
}
```

### Getting All Translations
If you need to see all available languages (e.g., for an admin editing form), append `?with_translations=1`.

**Example Request**: `GET /api/v1/pages/1?with_translations=1`
**Response**:
```json
{
  "id": 1,
  "title": "About Us",
  "translations": {
    "title": {
      "en": "About Us",
      "id": "Tentang Kami"
    },
    "content": {
      "en": "...",
      "id": "..."
    }
  }
}
```

### Storing/Updating Content
You can save translations in two ways:

#### A. Single Language (Implicit)
Pass a string. It will be saved to the current active locale.
```json
{
  "title": "New Title"
}
```

#### B. Multiple Languages (Explicit)
Pass an object with locale keys.
```json
{
  "title": {
    "en": "New Title",
    "id": "Judul Baru"
  }
}
```

---

## 🧑‍💻 Developer Implementation

To make a new field translatable:
1.  Ensure the column is a `json` type in the migration.
2.  Use the `HasTranslations` trait in the Model.
3.  Add the field name to the `public $translatable = [...]` array in the Model.
