# Modul Tugas 2: Pembuatan Modul Pengalaman Kerja (Experience Module)

## 1. Deskripsi Tugas

Tugas ini berfokus pada pembuatan modul backend baru bernama `Experience` menggunakan package `nwidart/laravel-modules` di INI CMS. Modul ini akan menyediakan data riwayat karier profesional secara dinamis untuk dikonsumsi oleh Frontend Portfolio melalui REST API.

## 2. Kriteria Sukses & Ketentuan Spesifik Fitur

- **Arsitektur Modular:** Seluruh kode (Migration, Model, Controller, Routes, Request, Transformer) wajib diisolasi di dalam folder `Modules/Experience/`.
- **Skema Database (Migration):** Tabel `experiences` harus memiliki kolom berikut:
    - `id` (Primary Key)
    - `position` (JSON - mendukung i18n lokal 'id' dan 'en')[cite: 2, 4]
    - `company` (String)[cite: 2, 4]
    - `start_date` (String/Date, contoh: "August 2024")[cite: 2, 4]
    - `end_date` (String/Date, contoh: "Present" atau "January 2025")[cite: 2, 4]
    - `description` (JSON - menyimpan detail poin-poin tugas dalam format 'id' dan 'en')[cite: 2, 4]
    - `order` (Integer, default 0 untuk sorting manual)
    - `is_active` (Boolean, default: `true`, menyelaraskan dengan filter standar)
    - `timestamps` (`created_at` dan `updated_at`)
    - `softDeletes` (`deleted_at`)
- **Model Configuration:**
    - Gunakan traits: `HasFactory`, `SoftDeletes`
    - Isi properti `$fillable`: `['position', 'company', 'start_date', 'end_date', 'description', 'order', 'is_active']`
    - Isi properti `$casts`: `['position' => 'array', 'description' => 'array', 'is_active' => 'boolean']`
- **Transformers (API Resource):**
    - Standarisasi payload response JSON wajib menyertakan: `id`, `position`, `company`, `start_date`, `end_date`, `description`, `order`, `is_active`, `created_at`, `updated_at`, dan `deleted_at`.
- **Endpoint API (Public):**
    - `GET /api/experiences` : Mengembalikan daftar seluruh riwayat kerja yang aktif (`is_active => true`) dan diurutkan berdasarkan kolom `order` atau tanggal terbaru[cite: 2, 4].

## 3. Daftar Tugas (Checklist Kerja AI)

### [ ] Inisialisasi Modul & Struktur Database

- [ ] Jalankan perintah `php artisan module:make Experience` untuk membuat struktur modul dasar.
- [ ] Buat berkas migrasi database di dalam folder `Modules/Experience/Database/Migrations/` lengkap dengan kolom `is_active`, `timestamps`, dan `softDeletes`.
- [ ] Buat berkas `ExperienceSeeder.php` untuk mengunci data riwayat asli dari CV ke database[cite: 1].

### [ ] Pembuatan Model & Controller API

- [ ] Buat model `Experience.php` di dalam modul dengan konfigurasi `$casts` dan trait `SoftDeletes`.
- [ ] Buat `ExperienceController.php` dan buat API Resource `ExperienceResource.php`.
- [ ] Daftarkan route API public dan protected di `Modules/Experience/Routes/api.php`.

### [ ] Pengujian Integrasi (Feature Testing)

- [ ] Buat berkas tes feature `Modules/Experience/Tests/Feature/ExperienceApiTest.php`.
- [ ] Pastikan pengujian memverifikasi status HTTP 200, penanganan soft deletes, status `is_active`, dan format payload data lengkap.

## 4. Validasi Akhir

- [ ] `php artisan test --filter=ExperienceApiTest` (Wajib lolos 100%)
