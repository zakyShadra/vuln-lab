<?php
// Simulasi "layanan internal" yang cuma boleh dipanggil dari server itu
// sendiri (mis. microservice pembayaran internal), bukan dari internet
// secara langsung. Proteksinya cuma cek IP asal request — dan itu justru
// yang berhasil dilewati lewat SSRF di import_image.php, karena dari sudut
// pandang layanan ini, request SSRF ATAU request langsung ke localhost
// sama-sama datang dari 127.0.0.1.
require_once __DIR__ . '/../config.php';

$remote = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($remote, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    header('Content-Type: text/plain');
    echo 'Forbidden: endpoint ini cuma bisa diakses dari localhost.';
    exit;
}

header('Content-Type: text/plain');
echo "Internal service OK.\n" . FLAG_SSRF;
