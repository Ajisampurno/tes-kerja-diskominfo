TES KERJA DISKOMINFO

Aplikasi ini menggunakan framework Laravel
Sebelum memulai, pastikan Anda sudah menginstal:

PHP >= 7.3
Composer
MySQL atau database lain yang kompatibel

1. Clone Repository

2. Install Dependensi PHP
    composer install

3. Konfigurasi Environment
    cp .env.example .env

4. Edit file .env
    contoh:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=diskominfo
    DB_USERNAME=root
    DB_PASSWORD=

5. Generate Key Aplikasi
    php artisan key:generate

6. Jalankan Migration dan Seeder
    php artisan migrate --seed

7. Jalankan Server Lokal
    php artisan serve

8. Menjalankan Seeder Lagi (Opsional)
    php artisan migrate:refresh --seed
