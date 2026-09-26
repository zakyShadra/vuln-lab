<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$q = $_GET['q'] ?? '';
$results = [];
$error = '';

if ($q !== '') {
    // VULNERABLE: input pencarian ditempel langsung ke query (bukan
    // prepared statement) — memungkinkan UNION-based SQL injection kalau
    // jumlah kolom UNION cocok dengan SELECT ini (3 kolom).
    $sql = "SELECT id, name, price FROM products WHERE name LIKE '%$q%'";
    try {
        $results = get_db()->query($sql)->fetchAll();
    } catch (PDOException $e) {
        $error = 'Query error: ' . $e->getMessage();
    }
}

$pageTitle = 'Cari Produk';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card">
  <h1>Hasil Pencarian</h1>
  <form method="get">
    <label>Kata kunci</label>
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>">
    <br><br>
    <button type="submit">Cari</button>
  </form>
</div>

<?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

<?php if ($q !== '' && !$results && !$error): ?>
  <p class="muted">Tidak ada produk yang cocok dengan "<?= htmlspecialchars($q) ?>".</p>
<?php endif; ?>

<?php if ($results): ?>
<table>
  <tr><th>ID</th><th>Nama</th><th>Harga</th></tr>
  <?php foreach ($results as $r): ?>
    <tr><td><?= htmlspecialchars($r['id']) ?></td><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['price']) ?></td></tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
