# Modul Tugas 5 : Pembersihan Modul Backend yang Tidak Terpakai

## 1. Deskripsi Tugas

Tugas ini berfokus pada penghapusan modul-modul bawaan INI CMS yang tidak digunakan oleh aplikasi portofolio. Langkah ini bertujuan untuk merampingkan ukuran repositori, mempercepat proses autoloading, dan memastikan _branch_ portofolio ini benar-benar bersih dan terfokus.

## 2. Kriteria Sukses & Ketentuan Spesifik

- **Modul yang Wajib Dihapus:**
    - `Modules/Blog` (Seluruh pos, kategori, dan logika blog ditiadakan).
    - `Modules/Media` (Jika ada controller/routes bawaan yang tidak terikat dengan Spatie Medialibrary utama).
    - `Modules/Menu` (Navigasi frontend menggunakan hardcoded smooth-scroll / tab Vue, sehingga modul menu dinamis tidak diperlukan).
- **Modul yang Wajib Dipertahankan:**
    - `Portfolio`, `Experience`, `Skill`, `Contact`, `Setting`, `Auth`, dan `Acl`[cite: 2, 4].
- **Kondisi Akhir:** Aplikasi Laravel tidak menghasilkan eror _class not found_ dan perintah `php artisan route:list` berjalan normal tanpa merujuk ke modul yang dihapus.

## 3. Daftar Tugas (Checklist Kerja AI)

- [x] Hapus folder fisik modul `Modules/Blog`, `Modules/Media` (jika independen), dan `Modules/Menu`.
- [x] Periksa berkas `modules_statuses.json` di root project dan hapus referensi kunci dari modul-modul yang telah dibuang.
- [x] Hapus file migrasi bawaan dari modul yang dihapus jika ada di folder database utama.
- [x] Jalankan `composer dump-autoload` untuk membersihkan cache penamaan kelas PHP.

## 4. Validasi Akhir

- [x] `php artisan route:list` (Memastikan tidak ada route yang rusak)
- [x] `php artisan test` (Seluruh test suite bawaan modul tersisa wajib tetap lolos 100%)
