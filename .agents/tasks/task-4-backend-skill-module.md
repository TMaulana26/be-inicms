# Modul Tugas 4 : Pembuatan Modul Keahlian Teknis (Skill Module)

## 1. Deskripsi Tugas

Tugas ini berfokus pada pembuatan modul backend baru bernama `Skill` menggunakan package `nwidart/laravel-modules` untuk mengelola daftar keahlian teknis (Technical Skills) yang akan dirender sebagai _badges_ di Frontend Portfolio.

## 2. Kriteria Sukses & Ketentuan Spesifik Fitur

- **Arsitektur Modular:** Seluruh kode wajib berada di bawah namespace `Modules/Skill/`.
- **Skema Database (Migration):** Tabel `skills` harus memiliki kolom berikut:
    - `id` (Primary Key)
    - `name` (String, contoh: "Vue.js", "Laravel", "Tailwind CSS")[cite: 2, 4]
    - `category` (String, contoh: "backend", "frontend", "tools")
    - `order` (Integer, default 0 untuk pengurutan tampilan)
    - `is_active` (Boolean, default: `true`)
    - `timestamps` (`created_at` dan `updated_at`)
    - `softDeletes` (`deleted_at`)
- **Model Configuration:**
    - Gunakan traits: `HasFactory`, `SoftDeletes`
    - Isi properti `$fillable`: `['name', 'category', 'order', 'is_active']`
    - Isi properti `$casts`: `['is_active' => 'boolean']`
- **Endpoint API (Public):**
    - `GET /api/skills` : Mengembalikan daftar seluruh keahlian yang aktif (`is_active => true`) diurutkan berdasarkan `order` untuk langsung di-looping oleh komponen `NeoBadge` di FE[cite: 2, 4].

## 3. Daftar Tugas (Checklist Kerja AI)

- [x] Jalankan perintah `php artisan module:make Skill`.
- [x] Buat berkas migrasi database dengan kolom `is_active`, `timestamps`, dan `softDeletes`.
- [x] Buat berkas `SkillSeeder.php` untuk mengunci daftar keahlian teknis asli Anda dari CV ke database.
- [x] Buat berkas model `Skill.php`, `SkillController.php`, dan `SkillResource.php`.
- [x] Daftarkan route di `Modules/Skill/Routes/api.php`.
- [x] Buat berkas tes feature `Modules/Skill/Tests/Feature/SkillApiTest.php` untuk memverifikasi status HTTP 200 dan filtrasinya.

## 4. Validasi Akhir

- [x] `php artisan test --filter=SkillApiTest` (Wajib lolos 100%)
