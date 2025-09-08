# DataForSEO Demo (Laravel)

Форма пошуку з **автодоповненням локацій** (CSV → seed у SQLite) і запитом до **DataForSEO SERP Advanced**.

## Вимоги
- PHP 8.2+ та Composer 2.x
- SQLite (за замовчуванням) або інша БД

> Frontend: **Bootstrap підключено локально** з `public/css/bootstrap.min.css`

## Швидкий старт

# 1) Встановити залежності PHP
composer install
# 2) Оточення

Створи локальний файл конфігурації (якщо ще немає):

cp .env.example .env
Відкрий .env і заповни:

APP_NAME=DataForSEODemo
APP_ENV=local
APP_URL=http://127.0.0.1:8000

# База (SQLite)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/project/database/database.sqlite

# DataForSEO
DFS_LOGIN=your_login
DFS_PASS=your_password

# 3) База даних

Створи файл БД (для SQLite):

mkdir -p database
touch database/database.sqlite
# 4 Міграції та сід:

php artisan migrate --force
php artisan db:seed --class=LocationsSeeder --force

# 5 Запуск
php artisan serve

## Примітка. Структура (важливі файли)
app/
    Http/Controllers/SEOApiController.php
    Http/Requests/SEOApiRequest.php
    Models/Location.php
database/
    migrations/xxxx_xx_xx_xxxxxx_create_locations_table.php
    seeders/LocationsSeeder.php
    factories/LocationFactory.php
    seeders/data/locations_and_languages_databases_2025_08_05.csv
resources/views/main-form.blade.php
routes/web.php
public/css/bootstrap.min.css   ← локальний Bootstrap
