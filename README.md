# Softoria demo (Laravel)

A search form with **location autocomplete** (CSV → seed into SQLite) and a request to **DataForSEO SERP Advanced**.

## Requirements

- PHP 8.2+ and Composer 2.x
- 
- SQLite (by default) or another database

> Frontend: **Bootstrap is included locally** from `public/css/bootstrap.min.css`.

## Quick Start

### 1) Install PHP dependencies

composer install

### 2) Environment

Create a local env file (if you don’t have one yet):

cp .env.example .env

Open .env and fill in:

APP_NAME=DataForSEODemo

APP_ENV=local

APP_URL=http://127.0.0.1:8000

#### Database (SQLite)

DB_CONNECTION=sqlite

DB_DATABASE=/absolute/path/to/project/database/database.sqlite

#### DataForSEO
DFS_LOGIN=your_login

DFS_PASS=your_password

If APP_KEY is empty, generate it:

php artisan key:generate

### 3) Database

Create the database file (for SQLite):

mkdir -p database

touch database/database.sqlite

### 4) Migrations & seed
   php artisan migrate --force

   php artisan db:seed --class=LocationsSeeder --force
### 5) Run the app
php artisan serve

### Project structure (key files)
app/

- Http/Controllers/SEOApiController.php

- Http/Requests/SEOApiRequest.php

- Models/Location.php


- database/

  - migrations/xxxx_xx_xx_xxxxxx_create_locations_table.php
    
  - seeders/LocationsSeeder.php
    
  - factories/LocationFactory.php
    
  - seeders/data/locations_and_languages_databases_2025_08_05.csv
    
  - resources/views/main-form.blade.php
  
  - routes/web.php
  
  - public/css/bootstrap.min.css   ← local Bootstrap

### Note (cURL error 60)

If you get a cURL error 60 when calling the API, open your php.ini, uncomment curl.cainfo and openssl.cafile, download the latest cacert.pem, and set the absolute path to it in those options.

curl.cainfo = "C:\certs\cacert.pem"

openssl.cafile = "C:\certs\cacert.pem"
