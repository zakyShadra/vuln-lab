<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
$db = get_db();

// VULNERABLE: tidak ada pengecekan apakah order ini benar-benar milik user
// yang sedang login (bandingkan $order['user_id'] dengan current_user()['id']
// tidak pernah dilakukan) — IDOR klasik.
$stmt = $db->prepare(
    'SELECT o.*, p.name AS product_name FROM orders o JOIN products p ON p.id = o.product_id WHERE o.id = ?'
);
$stmt->execute([$id]);
$order = $stmt->fetch();

$pageTitle = 'Detail Order';
require __DIR__ . '/includes/layout_top.php';

if (!$order) {
    echo '<div class="card"><p>Order tidak ditemukan.</p></div>';
} else {
?>
<div class="card">
  <h1>Order #<?= (int) $order['id'] ?></h1>
  <p>Produk: <?= htmlspecialchars($order['product_name']) ?></p>
  <p>Harga dibayar: <?= money((int) $order['price_paid']) ?></p>
  <p>Catatan internal CS: <?= htmlspecialchars($order['internal_note']) ?></p>
</div>
<?php
}
require __DIR__ . '/includes/layout_bottom.php';
