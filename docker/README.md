# Aanaya Docker: Nginx + PHP-FPM

Arsitektur container:

```text
Browser -> Nginx :80 -> PHP-FPM :9000 -> Laravel -> Supabase / Cloudinary
                 -> static file langsung dari /var/www/public
                                      Laravel -> Redis (cache/session/queue)
```

## Menjalankan

```bash
docker compose up -d --build
docker compose exec app php artisan migrate --force
```

Aplikasi tersedia di `http://localhost:8000`. Port dapat diganti tanpa mengedit
Compose:

```bash
APP_PORT=8080 docker compose up -d
```

Compose mengoverride `APP_URL` dan `GOOGLE_REDIRECT_URI` berdasarkan `APP_PORT`
agar OAuth lokal tidak memakai domain production dari `.env`. Daftarkan callback
lokal, misalnya `http://localhost:8000/auth/google/callback`, sebagai Authorized
redirect URI pada Google Cloud Console.

## Perintah operasional

```bash
docker compose ps
docker compose logs -f nginx app
docker compose exec app php artisan about
docker compose exec app php artisan optimize
docker compose exec app php artisan queue:restart
docker compose exec redis redis-cli ping
docker compose exec redis redis-cli info memory
docker compose down
```

Jangan menjalankan `php artisan migrate --force` otomatis pada setiap startup.
Jalankan sebagai release command agar beberapa replica tidak melakukan migrasi
secara bersamaan.

## Production

- Set `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` HTTPS.
- TLS dapat dihentikan oleh load balancer / reverse proxy di depan container
  Nginx, atau ditambahkan langsung pada konfigurasi Nginx.
- Jalankan `php artisan optimize` setelah environment production tersedia.
- Database tetap menggunakan PostgreSQL Supabase melalui environment Laravel.
- Upload media utama tetap langsung menuju Cloudinary.
- Docker menggunakan Redis persisten untuk cache, session, dan queue. Volume
  `aanaya-redis` tetap tersedia ketika container dibuat ulang.
- Di Laravel Cloud, gunakan resource KV/Redis-compatible dan ganti kredensial
  `REDIS_*` melalui environment production; hostname `redis` hanya berlaku di
  jaringan Docker Compose.
