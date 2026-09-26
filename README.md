# NUSANTARA MART (web tumbal)

Toko online fiktif, sengaja penuh kerentanan, dipakai sebagai target latihan
untuk menu **Latihan Soal (Advanced)** di SIGNAL/90. Satu web ini menampung
banyak studi kasus berbeda — tidak perlu bikin web tumbal baru per studi
kasus.

**PHP + SQLite polos** — tidak butuh server yang harus dijaga terus-menerus.
Deploy-nya sama seperti hosting website biasa: upload, selesai, host yang
menjaga tetap menyala.

## Deploy ke Domainesia (atau shared hosting PHP mana pun)

1. Upload seluruh isi folder ini ke `public_html` (atau ke folder subdomain
   kalau dipasang di subdomain, misal `lab.ices.my.id`).
2. Pastikan folder `data/` dan `uploads/` bisa ditulis oleh PHP
   (`chmod 775 data uploads` lewat File Manager cPanel kalau perlu).
3. Buka domainnya — database SQLite otomatis dibuat & diisi data awal saat
   pertama kali diakses (lihat `includes/db.php`).
4. Selesai. Tidak ada langkah lain, tidak ada proses yang perlu dijalankan manual.

Untuk reset total (hapus semua akun/order yang dibuat pengguna): hapus file
`data/*.sqlite`, nanti otomatis dibuat ulang dari awal saat diakses lagi.

## Login default

| Username | Password | Role |
|---|---|---|
| admin | AdminNusantara2024! | admin |
| alice | alice123 | user |
| bob | bob12345 | user |

## Indeks kerentanan (referensi internal — dipakai studi kasus di SIGNAL/90)

| # | Kerentanan | Lokasi | Flag |
|---|---|---|---|
| 1 | SQL Injection — auth bypass | `login.php` | `FLAG{sqli_login_bypass_9f3a21}` |
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
- Kalau hosting-mu menonaktifkan `shell_exec()` (beberapa shared hosting
  mematikan fungsi exec demi keamanan), studi kasus command injection
  (#6) tidak akan berfungsi — itu keputusan keamanan yang sah dari host,
  bukan bug di lab ini.
- Reset data kapan saja dengan menghapus `data/*.sqlite`.
