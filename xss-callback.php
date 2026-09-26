<?php
// Endpoint "berharga" yang cuma bisa diakses efeknya kalau payload XSS-mu
// benar-benar tereksekusi di context halaman (fetch/XHR dari dalam browser).
require_once __DIR__ . '/config.php';
header('Content-Type: text/plain');
echo FLAG_STORED_XSS;
