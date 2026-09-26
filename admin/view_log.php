<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (($_COOKIE['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$file = $_GET['file'] ?? 'access.log';
// VULNERABLE: nama file digabung langsung ke path tanpa validasi apa pun —
// tidak ada basename(), tidak ada pengecekan "../". Path traversal /
// local file inclusion klasik: siapa pun yang bisa akses halaman ini bisa
// baca file APAPUN yang bisa dibaca oleh proses web server, bukan cuma
// file di dalam folder logs/.
$path = __DIR__ . '/../logs/' . $file;

$content = '';
$error = '';
if (file_exists($path) && is_file($path)) {
    $content = file_get_contents($path);
} else {
    $error = 'File tidak ditemukan: ' . htmlspecialchars($file);
}

$pageTitle = 'Log Viewer';
require __DIR__ . '/../includes/layout_top.php';
?>
<div class="card">
  <h1>Log Viewer</h1>
  <p class="muted">Lihat file log aplikasi (dipakai tim ops buat troubleshooting cepat).</p>
  <form method="get">
    <label>Nama file (di dalam folder logs/)</label>
    <input type="text" name="file" value="<?= htmlspecialchars($file) ?>" placeholder="access.log">
    <br><br>
    <button type="submit">Lihat</button>
  </form>
  <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
  <?php if ($content !== ''): ?>
    <h3>Isi file:</h3>
    <pre style="background:#111;color:#7ed957;padding:12px;border-radius:6px;overflow-x:auto; white-space:pre-wrap;"><?= htmlspecialchars($content) ?></pre>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/layout_bottom.php'; ?>
