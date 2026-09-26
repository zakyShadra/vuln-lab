<?php
/**
 * Konfigurasi dasar NUSANTARA MART.
 * Sengaja sederhana (termasuk beberapa hal yang SENGAJA tidak aman) —
 * ini web tumbal untuk latihan, bukan aplikasi produksi.
 */

define('APP_NAME', 'NUSANTARA MART');
define('DB_PATH', __DIR__ . '/data/nusantara.sqlite');

// Flag tiap kerentanan. Ditampilkan otomatis oleh halaman terkait begitu
// eksploitasinya berhasil dilakukan.
define('FLAG_SQLI_LOGIN',    'FLAG{sqli_login_bypass_9f3a21}');
define('FLAG_SQLI_UNION',    'FLAG{union_select_extract_2b7cd4}');
define('FLAG_STORED_XSS',    'FLAG{stored_xss_review_persisted_7e11}');
define('FLAG_IDOR',          'FLAG{idor_order_leak_c04f88}');
define('FLAG_BROKEN_ACCESS', 'FLAG{broken_access_control_role_cookie_a91d}');
define('FLAG_CMDI',          'FLAG{command_injection_ping_5d3e70}');
define('FLAG_UPLOAD_RCE',    'FLAG{insecure_upload_webshell_rce_6c2a}');
define('FLAG_SSRF',          'FLAG{ssrf_internal_endpoint_reached_e814}');
define('FLAG_BIZ_LOGIC',     'FLAG{price_tampering_checkout_bf209}');
define('FLAG_INFO_LEAK',     'FLAG{backup_file_exposed_3a77f1}');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
