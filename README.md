# Infra Laravel — VPS 2GB (sisa 1GB), budget 512MB

## Perubahan struktur folder

Repo lama:
```
ampr/
  Dockerfile
  php/
  src/
docker-compose.yml
```

Repo baru (biar Dockerfile & nginx bisa dipakai bareng utk app kedua nanti):
```
apps/
  ampr/
    src/          <- pindahkan isi ampr/src/ lama ke sini
  app2/
    src/          <- nanti, saat app kedua siap
docker/
  php/
    Dockerfile
    entrypoint.sh
    php.ini
    opcache.ini
    www.conf
  nginx/
    Dockerfile
    nginx.conf
    conf.d/
      ampr.conf
      app2.conf.example
docker-compose.yml
.dockerignore
.github/workflows/
  deploy.yml
  lint.yml
  tests.yml
```

Langkah migrasi:
```bash
mkdir -p apps/ampr
git mv ampr/src apps/ampr/src
git rm -r ampr/Dockerfile ampr/php
```

## Bug yang diperbaiki dari setup lama

1. **Network mismatch**: service `ampr` pakai `app-network`, `redis` pakai
   `app_network` — beda nama, jadi app **tidak pernah** bisa konek ke redis.
   Sekarang konsisten `app-network`.
2. **CI tidak pernah build image**: job `build` di `deploy.yml` lama isinya
   kosong. Artinya `docker compose pull` di VPS narik image `latest` yang
   entah kapan terakhir di-build manual — sementara kode aktual jalan dari
   bind-mount host. Sekarang CI benar-benar build & push ke GHCR.
3. **Kode di-bind-mount dari host** (`./ampr/src:/var/www`): kode app bisa
   ditulis dari dalam container ke host, dan sebaliknya. Kalau ada RCE di
   app, attacker bisa nanam webshell permanen. Sekarang kode di-`COPY` ke
   image saat build (image immutable), root filesystem container
   **read-only**, hanya `storage/` dan `bootstrap/cache/` yang writable
   (lewat named volume terpisah).
4. **Tidak ada web server**: `php-fpm` sendirian tidak bisa serve HTTP,
   Cloudflare Tunnel butuh sesuatu yang bicara HTTP di depannya. Ditambah
   1 nginx (dipakai bareng untuk semua app, hemat memori vs nginx per app).
5. **lint.yml/tests.yml** jalan di root, padahal app ada di
   `apps/ampr/src` — ditambah `working-directory`.

## Kenapa nginx & php-fpm container terpisah tapi 1 nginx untuk semua app?

- Terpisah dari php-fpm: least privilege — nginx tidak butuh PHP runtime,
  php-fpm tidak butuh network stack HTTP publik.
- 1 nginx untuk semua app (bukan 1 nginx per app): hemat ~20-30MB per app
  yang tidak dibuild, dan cuma 1 titik masuk yang perlu di-expose ke
  Cloudflare Tunnel. nginx bedakan app lewat `server_name` (Host header),
  jadi di Cloudflare Tunnel kamu tinggal arahkan beberapa hostname publik
  (mis. `ampr.domainmu.com`, `app2.domainmu.com`) ke target yang sama:
  `http://127.0.0.1:8080`.

## Kenapa public/ nyampe ke nginx padahal kode di-COPY ke image php-fpm?

nginx butuh baca file statis (`.js`, `.css`, gambar) langsung dari disk,
tapi kode Laravel (termasuk `public/`) ada di dalam image **php-fpm**, bukan
image nginx. Solusinya: `entrypoint.sh` di container php-fpm nge-`rsync`
folder `public/` (yang sudah ter-bake di image, read-only) ke sebuah named
volume (`ampr_public`) tiap kali container start. Volume yang sama
di-mount read-only di container nginx. Jadi tetap immutable/image-based,
tanpa bind-mount ke host.

## Rootless total (bukan cuma non-root user)

- **php-fpm**: master process jalan sebagai `www-data` dari awal (bukan
  root yang drop privilege), lewat kombinasi `USER www-data` di Dockerfile
  + `user/group www-data` di `www.conf`.
- **nginx**: dipaksa `USER nginx` di Dockerfile, listen di port **8080**
  (bukan 80) supaya tidak butuh capability `NET_BIND_SERVICE` sama sekali.
- Semua service: `cap_drop: [ALL]`, tanpa `cap_add` apa pun, plus
  `no-new-privileges:true` dan `read_only: true` root filesystem
  (kecuali `/tmp` via tmpfs dan volume writable yang eksplisit).

## Budget memori (target ≤ 512MB total)

| Service | mem_limit | Catatan |
|---|---|---|
| nginx | 32 MB | 1 worker process, semua temp path di tmpfs |
| ampr (php-fpm) | 190 MB | opcache 64MB (shared antar worker) + `pm=ondemand` max 3 worker |
| app2 (nanti) | 190 MB | sama seperti ampr |
| redis | 80 MB | `maxmemory 48mb` + overhead proses |
| **Total (2 app)** | **492 MB** | buffer ~20MB dari batas 512MB |
| **Total (1 app, sekarang)** | **302 MB** | ampr + nginx + redis saja |

Catatan penting: `memory_limit = 128M` di `php.ini` adalah **batas per
request**, bukan alokasi pasti. Worst-case teoritis kalau 3 worker sama-sama
kena memory limit maksimum = 384MB > 190MB container limit → OOM-kill.
Dengan 5 user aktif ini nyaris tidak mungkin terjadi (request Laravel
normal biasanya 20-40MB), tapi:
- pantau realita dengan `docker stats` beberapa hari pertama,
- kalau ada OOM-kill (`docker inspect <container> | grep OOMKilled`),
  turunkan `pm.max_children` jadi 2, atau naikkan `mem_limit` sedikit
  (masih ada headroom di 1GB VPS untuk 1 app).

**Saran tambahan** (di luar file ini, dilakukan langsung di VPS): karena
memori pas-pasan, tambahkan swap 512MB–1GB sebagai jaring pengaman:
```bash
sudo fallocate -l 1G /swapfile && sudo chmod 600 /swapfile
sudo mkswap /swapfile && sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```
Swap tidak dipakai untuk kerja normal (masih dalam `mem_limit`), hanya
jaring pengaman kalau ada lonjakan sesaat.

## `pm.max_children` & CPU

`cpus` di compose ini asumsi VPS 2 vCPU (nginx 0.25 + ampr 0.5 + app2 0.5
+ redis 0.25 = 1.5). Kalau VPS-mu 1 vCPU, turunkan proporsional (mis. jadi
setengahnya) — ini cuma soft-limit/throttle, jadi tidak masalah kalau
totalnya melebihi jumlah core asli, tapi lebih presisi kalau disesuaikan.

## Yang perlu disiapkan manual di VPS (tidak ikut di repo)

```
/home/satella/laravel/
  env/
    ampr.env   <- chmod 600, isi APP_KEY, DB_*, REDIS_HOST=redis, dst
    app2.env   <- nanti
```
`REDIS_HOST` di `.env` app harus diisi `redis` (nama service), bukan
`127.0.0.1`.

## Cara menambah app kedua (`app2`)

1. `apps/app2/src/` — taruh kode Laravel app kedua.
2. `docker-compose.yml` — uncomment blok service `app2` + volume terkait,
   dan uncomment mount `app2_public` di service `nginx`.
3. `docker/nginx/conf.d/app2.conf.example` → rename jadi `app2.conf`,
   sesuaikan `server_name`.
4. `.github/workflows/deploy.yml` — uncomment entry `app2` di `matrix.include`.
5. Buat `/home/satella/laravel/env/app2.env` di VPS.
6. Di Cloudflare Tunnel: tambah public hostname baru untuk app2, arahkan
   ke `http://127.0.0.1:8080` (sama seperti ampr — nginx yang membedakan
   lewat `server_name`).

## Cloudflare Tunnel (di luar proyek ini, cuma catatan)

Tunnel diarahkan ke `http://127.0.0.1:8080` (bukan langsung ke container),
karena nginx cuma expose ke `127.0.0.1` di host, tidak pernah ke interface
publik. Beberapa hostname publik boleh mengarah ke target yang sama; nginx
yang merutekan berdasarkan `Host` header (`server_name`).

## Hal yang perlu kamu sesuaikan sendiri

- `disable_functions` di `php.ini` cukup ketat (matiin `exec`,
  `shell_exec`, `proc_open`, dst). Kalau ada dependency Laravel yang
  butuh salah satu (mis. lewat `Process` facade), longgarkan baris ini.
- `server_name` di `docker/nginx/conf.d/*.conf` masih placeholder
  (`ampr.example.com`) — ganti sesuai hostname tunnel-mu.
- `ghcr.io/satellacodes/...` — ganti kalau nama org/repo GHCR-mu beda.
