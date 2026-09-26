# NUSANTARA MART (web tumbal)

Toko online fiktif, sengaja penuh kerentanan, dipakai sebagai target latihan
untuk menu **Latihan Soal (Advanced)** di SIGNAL/90. Satu web ini menampung
banyak studi kasus berbeda — tidak perlu bikin web tumbal baru per studi
kasus.

**PHP + SQLite polos, tidak butuh fungsi berbahaya apa pun** (tidak ada
`shell_exec`/`exec`/`system` di manapun dalam kode) — didesain supaya
**semua 10 kerentanan jalan di hosting gratis PALING ketat sekalipun**, tanpa
kartu kredit, tanpa Docker.

## Deploy ke shared hosting PHP gratis (Recommended — InfinityFree, 000webhost, dsb)

1. Daftar akun gratis (tanpa kartu) di [InfinityFree](https://infinityfree.net)
   atau sejenisnya, buat satu hosting baru.
2. Upload seluruh isi folder ini ke `htdocs`/`public_html` lewat File Manager
   atau FTP.
3. Pastikan folder `data/` dan `uploads/` bisa ditulis PHP (`chmod 775`).
4. Buka domainnya — database SQLite otomatis dibuat & diisi data awal saat
   pertama kali diakses.

Untuk reset total: hapus file `data/*.sqlite`, otomatis dibuat ulang dari nol
saat diakses lagi.

**Satu catatan jujur:** studi kasus #7 (upload webshell) melibatkan upload
file `.php` — sebagian host gratis punya scanner malware otomatis yang
kadang menghapus file semacam ini. Kalau kejadian, itu bukan bug di lab ini;
9 studi kasus lain tetap 100% gak kepengaruh sama sekali.

## Alternatif: Docker (Render.com, Fly.io, dsb)

Kalau kamu gak masalah verifikasi kartu (biasanya cuma authorization $1,
gak kepotong) dan mau environment yang lebih terisolasi/gak kena scanner
host orang lain, `Dockerfile` + `docker-entrypoint.sh` di folder ini sudah
disiapkan & di-test buat itu:

1. Push folder ini ke repo GitHub.
2. Di Render: **New + → Web Service** → connect repo → Environment: **Docker**
   → Instance: **Free** → Deploy.
3. URL publiknya (`https://nama-service.onrender.com`) dipakai buat isi
   `TARGET.baseUrl` di `roadmap-app/data/exercises.js`.

Instance gratis Render tidur setelah ~15 menit nganggur dan bangun otomatis
saat ada request (delay beberapa detik) — dan karena gak ada persistent disk
di tier gratis, data SQLite ke-reset tiap instance restart (termasuk tiap
abis idle), bukan cuma pas redeploy.

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
| 6 | Path Traversal / LFI | `admin/view_log.php?file=` | `FLAG{path_traversal_lfi_5d3e70}` (di `secret/db_credentials.txt`, dibaca lewat `../`) |
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
- Reset data kapan saja dengan menghapus `data/*.sqlite`.
