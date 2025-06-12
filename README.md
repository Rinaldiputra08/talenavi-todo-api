<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Talenavi Todo API Service

Proyek ini dibangun menggunakan **Laravel Framework 12.18.0** dan PHP 8.3.14.

---

## Table of Contents

* [Fitur](#fitur)
* [Persyaratan Sistem](#persyaratan-sistem)
* [Instalasi](#instalasi)
* [Konfigurasi](#konfigurasi)
* [Database Migrations & Seeding](#database-migrations--seeding)

---

## Fitur

Proyek ini mengimplementasikan tiga endpoint API utama sesuai dengan requirements:

* **API Create Todo List:**
    * Membuat entri Todo baru dengan validasi input yang ketat (judul, tanggal jatuh tempo, status, prioritas, dll.).
    * Mendukung field opsional (`assignee`) dan default values (`time_tracked`, `status`).
* **API Get Todo List to Generate Excel Report:**
    * Menghasilkan file Excel (.xlsx) dari data Todo.
    * Kolom laporan: `Title`, `Assignee`, `Due Date`, `Time Tracked`, `Status`, `Priority`.
    * Dilengkapi dengan baris ringkasan di bagian bawah (Total Todo, Total Waktu Terlacak) dari data yang difilter.
    * Mendukung filtering komprehensif berdasarkan semua field (partial match, multiple values, rentang tanggal/numerik).
* **API Get Todo List to Provide Chart Data:**
    * Menyediakan data agregat dalam format JSON untuk kebutuhan chart/grafik.
    * Mendukung tiga jenis ringkasan:
        * **Status Summary:** Jumlah Todo per status (`pending`, `open`, `in_progress`, `completed`).
        * **Priority Summary:** Jumlah Todo per prioritas (`low`, `medium`, `high`).
        * **Assignee Summary:** Detail Todo per assignee (total Todo, total pending Todo, total waktu terlacak untuk Todo yang selesai).

---

## Persyaratan Sistem

Pastikan sistem Anda memenuhi persyaratan berikut untuk menjalankan aplikasi:

* PHP >= 8.2
* Composer
* MySQL (atau database lain yang didukung Laravel, seperti PostgreSQL, SQLite)
* Postman (untuk pengujian API)

---

## Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek secara lokal:

1.  **Clone repositori:**
    ```bash
    git clone https://github.com/Rinaldiputra08/talenavi-todo-api.git
    cd your-repo-name
    ```

2.  **Instal dependensi Composer:**
    ```bash
    composer install
    ```

3.  **Atur file `.env`:**
    Jika sudah ada file `.env.example` bawaan ketika clone project maka anda hanya perlu rename file tersebut menjadi `.env` jika tidak ada maka bisa jalankan:

    ```bash
    cp .env.example .env
    ```
    Jika sudah ada file 

5.  **Buat application key:**
    ```bash
    php artisan key:generate
    ```
---

## Konfigurasi

Edit file `.env` Anda dan sesuaikan koneksi database Anda:

```dotenv
APP_NAME="Talenavi Todo API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=talenavi_todo_db # Ganti dengan nama database Anda
DB_USERNAME=root             # Ganti dengan username database Anda
DB_PASSWORD=                 # Ganti dengan password database Anda

# ... (bagian lain dari .env bisa tetap standar)
```

---

## Database Migrations & Seeding

1. **Jalankan migrasi database dan seed:**
    ```bash
    php artisan migrate --seed
    ```
    Catatan: Secara default, file routes/api.php tidak ada di instalasi Laravel 11/12 yang baru. Untuk mengaktifkan routing API dan Laravel Sanctum (opsional, namun direkomendasikan       untuk API), pastikan Anda telah menjalankan:

    ```bash
   php artisan install:api
    ```
2. Jalankan server Laravel:
   
   ```bash
   php artisan serve
    ```
   Aplikasi akan berjalan di http://localhost:8000 (atau port lain jika sudah dipakai), lalu mulai pengujian dengan postman
---
