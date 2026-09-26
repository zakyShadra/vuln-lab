<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $_SESSION['cart'][] = (int) $_POST['product_id'];
    header('Location: cart.php');
    exit;
}

if (isset($_GET['remove'])) {
    $idx = (int) $_GET['remove'];
    unset($_SESSION['cart'][$idx]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header('Location: cart.php');
    exit;
}

$db = get_db();
$items = [];
$total = 0;
foreach ($_SESSION['cart'] as $i => $pid) {
    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$pid]);
    $p = $stmt->fetch();
    if ($p) {
        $items[] = ['idx' => $i, 'product' => $p];
        $total += (int) $p['price'];
    }
}

$pageTitle = 'Keranjang';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card">
  <h1>Keranjang Belanja</h1>
  <?php if (!$items): ?>
    <p class="muted">Keranjang kosong. <a href="index.php">Belanja dulu</a>.</p>
  <?php else: ?>
    <table>
      <?php foreach ($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['product']['name']) ?></td>
          <td><?= money((int) $it['product']['price']) ?></td>
          <td><a href="cart.php?remove=<?= (int) $it['idx'] ?>">Hapus</a></td>
        </tr>
      <?php endforeach; ?>
      <tr><th>Total</th><th><?= money($total) ?></th><th></th></tr>
    </table>
    <p><a class="btn" href="checkout.php">Lanjut ke Checkout</a></p>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
