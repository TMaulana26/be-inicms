# Modul Tugas 3: Pembuatan Modul Proyek Portofolio (Portfolio Module)

## 1. Deskripsi Tugas

Tugas ini berfokus pada pembuatan modul backend baru bernama `Portfolio` menggunakan package `nwidart/laravel-modules`. Modul ini bertugas mengelola data karya proyek web Anda, terintegrasi dengan `Spatie Laravel Medialibrary` untuk aset gambar, dan menyediakannya ke Frontend via REST API.

## 2. Kriteria Sukses & Ketentuan Spesifik Fitur

- **Arsitektur Modular:** Seluruh logika proyek harus berada di bawah namespace folder `Modules/Portfolio/`.
- **Skema Database (Migration):** Tabel `portfolio_projects` harus memiliki kolom berikut:
    - `id` (Primary Key)
    - `title` (String/JSON)[cite: 2, 4]
    - `slug` (String, unique)
    - `category` (String, contoh: 'FULLSTACK', 'FRONTEND')[cite: 2, 4]
    - `description` (JSON - deskripsi proyek dalam format lokal 'id' dan 'en')[cite: 2, 4]
    - `tech_stack` (JSON/Array - menyimpan tags teknologi)[cite: 2, 4]
    - `github_url` (String, nullable)[cite: 2, 4]
    - `demo_url` (String, nullable)[cite: 2, 4]
    - `is_active` (Boolean, default: `true`, menyelaraskan dengan filter standar)
    - `timestamps` (`created_at` dan `updated_at`)
    - `softDeletes` (`deleted_at`)
- **Model Configuration:**
    - Gunakan traits: `HasFactory`, `SoftDeletes`, `InteractsWithMedia` (Spatie)
    - Isi properti `$fillable`: `['title', 'slug', 'category', 'description', 'tech_stack', 'github_url', 'demo_url', 'is_active']`
    - Isi properti `$casts`: `['title' => 'array', 'description' => 'array', 'tech_stack' => 'array', 'is_active' => 'boolean']`
- **Transformers (API Resource):**
    - Standarisasi payload response JSON wajib menyertakan: `id`, `title`, `slug`, `category`, `description`, `tech_stack`, `github_url`, `demo_url`, `screenshot_url`, `is_active`, `created_at`, `updated_at`, dan `deleted_at`.
- **Endpoint API (Public):**
    - `GET /api/projects` : Mengembalikan daftar proyek aktif (`is_active => true`) lengkap beserta tautan URL gambar (`screenshot_url`)[cite: 2, 4].

## 3. Daftar Tugas (Checklist Kerja AI)

### [x] Inisialisasi Modul & Struktur Database

- [x] Jalankan perintah `php artisan module:make Portfolio` untuk menginisialisasi modul baru.
- [x] Buat berkas migrasi database untuk tabel `portfolio_projects` lengkap dengan kolom `is_active`, `timestamps`, dan `softDeletes`.
- [x] Buat berkas `PortfolioSeeder.php` untuk menginput data proyek nyata Anda ke dalam database.

### [x] Pembuatan Model & Controller API

- [x] Buat model `Project.php`, pasang trait `SoftDeletes`, dan konfigurasikan relasi media Spatie untuk koleksi `screenshot`.
- [x] Buat `ProjectController.php` public dan protected serta berkas `ProjectResource.php`.
- [x] Daftarkan route di `Modules/Portfolio/Routes/api.php`.

### [x] Pengujian Integrasi (Feature Testing)

- [x] Buat berkas tes feature `Modules/Portfolio/Tests/Feature/PortfolioApiTest.php`.
- [x] Pastikan pengujian memverifikasi status HTTP 200, penyaringan data `is_active`, mekanisme soft deletes, dan konversi URL Medialibrary.

## 4. Validasi Akhir

- [x] `php artisan test --filter=PortfolioApiTest` (Wajib lolos 100%)
