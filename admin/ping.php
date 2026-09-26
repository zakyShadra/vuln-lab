<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (($_COOKIE['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$output = '';
$host = $_GET['host'] ?? '';

if ($host !== '') {
    // VULNERABLE: input host ditempel langsung ke shell command tanpa
    // sanitasi maupun escapeshellarg(). Ini command injection klasik.
    $output = shell_exec('ping -c 1 ' . $host . ' 2>&1');
}

$pageTitle = 'Network Diagnostic';
require __DIR__ . '/../includes/layout_top.php';
?>
<div class="card">
  <h1>Network Diagnostic Tool</h1>
  <p class="muted">Cek konektivitas ke host tertentu (dipakai tim ops buat cek server pengiriman).</p>
  <form method="get">
    <label>Host / IP</label>
    <input type="text" name="host" value="<?= htmlspecialchars($host) ?>" placeholder="contoh: 8.8.8.8">
    <br><br>
    <button type="submit">Ping</button>
  </form>
  <?php if ($output !== ''): ?>
    <h3>Hasil:</h3>
    <pre style="background:#111;color:#7ed957;padding:12px;border-radius:6px;overflow-x:auto;"><?= htmlspecialchars($output) ?></pre>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/layout_bottom.php'; ?>
