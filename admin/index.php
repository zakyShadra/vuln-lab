<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// VULNERABLE: hanya mengecek cookie 'role' yang dikirim browser, tanpa
// verifikasi ulang ke session atau database. Cookie ini gampang diubah
// lewat DevTools (Application > Cookies) tanpa perlu login sebagai admin
// sama sekali — ini contoh broken access control lewat client-trusted state.
if (($_COOKIE['role'] ?? '') !== 'admin') {
    http_response_code(403);
    $pageTitle = 'Akses Ditolak';
    require __DIR__ . '/../includes/layout_top.php';
    echo '<div class="card"><p>Akses ditolak. Halaman ini khusus admin.</p></div>';
    require __DIR__ . '/../includes/layout_bottom.php';
    exit;
}

$pageTitle = 'Admin Dashboard';
require __DIR__ . '/../includes/layout_top.php';
?>
<div class="flag-banner"><?= FLAG_BROKEN_ACCESS ?></div>
<div class="card">
  <h1>Admin Dashboard</h1>
  <p>Selamat datang di panel admin <?= APP_NAME ?>.</p>
  <ul>
    <li><a href="/admin/users.php">Kelola Pengguna</a></li>
    <li><a href="/admin/ping.php">Network Diagnostic Tool</a></li>
    <li><a href="/admin/import_image.php">Import Gambar Produk dari URL</a></li>
  </ul>
</div>
<?php require __DIR__ . '/../includes/layout_bottom.php'; ?>
