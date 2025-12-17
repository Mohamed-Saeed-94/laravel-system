laravel-system (Laravel + Filament)

Admin panel / back-office built with Laravel and Filament.

Requirements

PHP >= 8.2

Composer

MySQL / MariaDB

Git

Node.js + npm (optional)

Installation (Local)

git clone https://github.com/USERNAME/laravel-system.git

cd laravel-system
composer install
cp .env.example .env
php artisan key:generate

Database
حدّث ملف .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=system
DB_USERNAME=root
DB_PASSWORD=

بعدها

php artisan migrate
php artisan make:filament-user
php artisan serve

Admin Panel
http://127.0.0.1:8000/admin

Notes

ممنوع رفع ملف .env

لازم composer install بعد أي clone
