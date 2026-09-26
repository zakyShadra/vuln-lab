# NUSANTARA MART (web tumbal)

Toko online fiktif, sengaja penuh kerentanan, dipakai sebagai target latihan
untuk menu **Latihan Soal (Advanced)** di SIGNAL/90. Satu web ini menampung
banyak studi kasus berbeda — tidak perlu bikin web tumbal baru per studi
kasus.

**PHP + SQLite polos** — tidak butuh server yang harus dijaga terus-menerus.

## Deploy ke Render.com (Recommended — gratis, tanpa kartu kredit, semua 10 kerentanan jalan)

Kenapa Render dan bukan shared hosting gratisan (InfinityFree, 000webhost,
dst): host gratisan biasanya **mematikan `shell_exec`/`exec`** di php.ini demi
keamanan mereka sendiri (bikin studi kasus #6 command injection gak akan
pernah jalan), dan scanner keamanan otomatis mereka sering **menghapus file
webshell** yang sengaja kamu upload buat studi kasus #7. Karena lab ini isinya
sengaja rentan, itu dua masalah nyata, bukan cuma teori.

Dengan Docker di Render, kamu yang kontrol environment-nya sendiri — sudah
di-build & di-test langsung (semua 10 flag terbukti bisa didapat) pakai
`Dockerfile` di folder ini.

1. Push folder `vuln-lab/` ini ke repo GitHub sendiri (boleh private).
2. Di [render.com](https://render.com), daftar (gratis, tanpa kartu kredit
   untuk web service gratisnya), lalu **New + → Web Service**.
3. Connect ke repo GitHub tadi, pilih **Environment: Docker** (Render otomatis
   deteksi `Dockerfile` di root repo).
4. Instance type pilih **Free**. Deploy.
5. Render kasih URL publik (`https://nama-service.onrender.com`) — itu yang
   dipakai buat isi `TARGET.baseUrl` di `roadmap-app/data/exercises.js`, atau
   diarahkan lewat CNAME record ke subdomain sendiri (mis. `nusantara-mart.ices.my.id`,
   sesuai pola di memory domain setup).

Catatan: instance gratis Render "tidur" setelah ~15 menit tanpa trafik, dan
"bangun" lagi otomatis (delay beberapa detik) begitu ada request masuk — tidak
masalah buat lab latihan, cuma jangan kaget kalau request pertama agak lambat.
Data SQLite bakal ke-reset tiap kali redeploy (bukan tiap idle) — anggap itu
fitur "reset lab" gratis, bukan bug.

## Alternatif: shared hosting PHP biasa (Domainesia, dsb)

Masih bisa, tinggal upload isi folder ini ke `public_html` dan pastikan
`data/` + `uploads/` writable (`chmod 775`) — database SQLite otomatis dibuat
saat pertama diakses. **Tapi cek dulu apakah `shell_exec()` diaktifkan di
hosting-mu**, kalau tidak, studi kasus #6 gak akan berfungsi (bukan bug, itu
keputusan keamanan host-nya).

Untuk reset total di mode ini: hapus file `data/*.sqlite`, nanti otomatis
dibuat ulang dari awal saat diakses lagi.

## Login default

| Username | Password | Role |
|---|---|---|
| admin | AdminNusantara2024! | admin |
| alice | alice123 | user |
| bob | bob12345 | user |

## Indeks kerentanan (referensi internal — dipakai studi kasus di SIGNAL/90)

| # | Kerentanan | Lokasi | Flag |
|---|---|---|---|
| 1 | SQL Injection — auth bypass | `login.php` → tampil di `admin/index.php` | `FLAG{sqli_login_bypass_9f3a21}` (cuma muncul kalau login-nya lewat injection, beda dari flag #5) |
| 2 | SQL Injection — UNION-based extraction | `search.php?q=` | `FLAG{union_select_extract_2b7cd4}` |
| 3 | Stored XSS | `product.php` (form ulasan) | `FLAG{stored_xss_review_persisted_7e11}` (lewat `xss-callback.php`) |
| 4 | IDOR | `order.php?id=` | `FLAG{idor_order_leak_c04f88}` (ada di order #2 milik bob) |
| 5 | Broken Access Control (client-trusted cookie) | `admin/index.php` | `FLAG{broken_access_control_role_cookie_a91d}` |
| 6 | Command Injection | `admin/ping.php?host=` | `FLAG{command_injection_ping_5d3e70}` (di `admin/flag_cmdi.txt`) |
| 7 | Unrestricted File Upload → RCE | `profile.php` (upload avatar) | `FLAG{insecure_upload_webshell_rce_6c2a}` (di `config.php`, dibaca lewat webshell) |
| 8 | SSRF | `admin/import_image.php` → `admin/secret_internal.php` | `FLAG{ssrf_internal_endpoint_reached_e814}` |
| 9 | Business Logic — price tampering | `checkout.php` (hidden field `price`) | `FLAG{price_tampering_checkout_bf209}` |
| 10 | Information Disclosure — exposed backup | `/backup.sql.bak` | `FLAG{backup_file_exposed_3a77f1}` |

Semua flag juga didefinisikan terpusat di `config.php`.

## Catatan

- **Jangan pernah pakai kode ini untuk aplikasi sungguhan.** Semua kerentanan
  di sini disengaja.
- `Dockerfile` + `docker-entrypoint.sh` cuma dipakai kalau deploy ke
  Render/Fly.io/dsb — abaikan kalau upload ke shared hosting biasa.
- Kalau hosting-mu menonaktifkan `shell_exec()` (beberapa shared hosting
  mematikan fungsi exec demi keamanan), studi kasus command injection
  (#6) tidak akan berfungsi — itu keputusan keamanan yang sah dari host,
  bukan bug di lab ini.
- Reset data kapan saja dengan menghapus `data/*.sqlite`.
