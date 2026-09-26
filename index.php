<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Katalog';
require __DIR__ . '/includes/layout_top.php';

$products = get_db()->query('SELECT id, name, description, price FROM products ORDER BY id')->fetchAll();
?>

<div class="card">
  <h1>Selamat datang di <?= APP_NAME ?></h1>
  <p class="muted">Toko peralatan komputer online. Ini web latihan (bukan toko sungguhan) — dipakai untuk studi kasus praktik keamanan siber.</p>
</div>

<div class="grid">
  <?php foreach ($products as $p): ?>
    <div class="product">
      <h3><a href="product.php?id=<?= (int) $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a></h3>
      <p class="muted"><?= htmlspecialchars($p['description']) ?></p>
      <p class="price"><?= money((int) $p['price']) ?></p>
    </div>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
