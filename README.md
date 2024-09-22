TES KERJA DISKOMINFO

Aplikasi ini menggunakan framework Laravel
Sebelum memulai, pastikan Anda sudah menginstal:

PHP >= 7.3
Composer
MySQL atau database lain yang kompatibel

1. Clone Repository
    https://github.com/Ajisampurno/tes-kerja-diskominfo.git
   
3. Install Dependensi PHP
    composer install

4. Konfigurasi Environment
    cp .env.example .env

5. Edit file .env
    contoh:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=diskominfo
    DB_USERNAME=root
    DB_PASSWORD=

6. Generate Key Aplikasi
    php artisan key:generate

7. Jalankan Migration dan Seeder
    php artisan migrate --seed

8. Jalankan Server Lokal
    php artisan serve

9. Menjalankan Seeder Lagi (Opsional)
    php artisan migrate:refresh --seed
