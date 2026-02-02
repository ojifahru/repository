# OF Digital repository

Aplikasi ini adalah **OF Digital repository** — repository dokumen Tri Dharma (mis. penelitian) berbasis Laravel + Filament.

Tujuan utamanya:

- Menyediakan **halaman publik** untuk melihat daftar dokumen, detail, halaman author, dan download.
- Menyediakan **panel admin** untuk mengelola data dokumen, author, kategori, fakultas, program studi, dan pengguna.

## Fitur

### Publik

- Beranda: `/`
- Daftar dokumen: `/dokumen` (pagination)
- Detail dokumen: `/dokumen/{id}`
- Download dokumen: `/dokumen/{id}/download`
- Halaman author: `/author/{id}`

Catatan: halaman publik hanya menampilkan dokumen dengan status `published`.

### Admin (Filament)

- Panel admin tersedia di `/admin`
- Role & permission menggunakan `spatie/laravel-permission` + Filament Shield

## Teknologi

- Laravel 12
- Filament v4
- Tailwind CSS v4 + Vite
- Pest (testing)

## Development (Local)

### Prasyarat

- PHP + Composer
- Node.js + npm
- Database: SQLite (default) atau MySQL/PostgreSQL

### Setup cepat

1) Install dependency:

```bash
composer install
npm install
```

2) Siapkan `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

3) Buat database & seed data awal:

```bash
php artisan migrate:fresh --seed
```

Seeder akan membuat role/permission awal dan user admin default.
Credential admin bisa diatur lewat `.env`:

```dotenv
ADMIN_EMAIL=admin@example.com
ADMIN_PASSWORD=password
```

4) Jalankan aplikasi:

Pilihan A (recommended, semua jalan bareng):

```bash
composer run dev
```

Pilihan B (manual, beberapa terminal):

```bash
php artisan serve
npm run dev
```

5) Buka aplikasi:

- Publik: `http://localhost:8000`
- Admin: `http://localhost:8000/admin`

## Production (Deploy)

Checklist umum untuk deploy ke server (Nginx/Apache):

### 1) Environment

Pastikan `.env` production berisi minimal:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...
APP_URL=https://domain-anda.tld

DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

Jika ingin men-setup admin pertama via seeder, set juga `ADMIN_EMAIL` dan `ADMIN_PASSWORD` lalu jalankan seeding sekali.

### 2) Install dependency (server)

```bash
composer install --no-dev --optimize-autoloader
```

### 3) Migrasi database

```bash
php artisan migrate --force
```

Jika perlu seed awal (sekali saja):

```bash
php artisan db:seed --class=Database\\Seeders\\InitialDataSeeder --force
```

### 4) Build asset frontend

```bash
npm ci
npm run build
```

Pastikan folder `public/build` ikut ter-deploy.

### 5) Cache & permission folder

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan folder ini writable oleh web server:

- `storage/`
- `bootstrap/cache/`

### 6) Queue & Scheduler (opsional tapi disarankan)

Jika menggunakan queue (default `QUEUE_CONNECTION=database`), jalankan worker via Supervisor/systemd:

```bash
php artisan queue:work --tries=1 --timeout=0
```

Untuk scheduler, pasang cron:

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting

- Error Vite manifest (Unable to locate file in Vite manifest): jalankan `npm run build` (production) atau `npm run dev` (development).
- Reset database lokal: `php artisan migrate:fresh --seed`.

## Catatan

Project ini menggunakan Laravel. Referensi framework: https://laravel.com/docs
