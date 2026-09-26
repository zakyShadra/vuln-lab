<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (($_COOKIE['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$result = '';
$url = $_POST['url'] ?? '';

if ($url !== '') {
    // VULNERABLE: server melakukan fetch ke URL manapun yang diberikan
    // admin, tanpa validasi apakah URL itu mengarah ke jaringan internal.
    // Ini SSRF — server bisa dipaksa mengakses layanan internal yang
    // seharusnya tidak bisa diakses langsung dari internet.
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $result = @file_get_contents($url, false, $ctx);
    if ($result === false) {
        $result = '(gagal mengambil konten dari URL tersebut)';
    }
}

$pageTitle = 'Import Gambar Produk';
require __DIR__ . '/../includes/layout_top.php';
?>
<div class="card">
  <h1>Import Gambar Produk dari URL</h1>
  <p class="muted">Tempel URL gambar produk dari supplier, server akan mengambilnya otomatis.</p>
  <form method="post">
    <label>URL Gambar</label>
    <input type="text" name="url" value="<?= htmlspecialchars($url) ?>" placeholder="https://supplier.example.com/gambar.jpg">
    <br><br>
    <button type="submit">Ambil Gambar</button>
  </form>
  <?php if ($result !== ''): ?>
    <h3>Konten yang diterima server:</h3>
    <pre style="background:#111;color:#7ed957;padding:12px;border-radius:6px;overflow-x:auto; white-space:pre-wrap;"><?= htmlspecialchars($result) ?></pre>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/layout_bottom.php'; ?>
