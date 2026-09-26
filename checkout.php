<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$db = get_db();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = (int) ($_POST['product_id'] ?? 0);
    // VULNERABLE: harga diambil dari field tersembunyi yang dikirim klien,
    // bukan di-lookup ulang dari database. Field ini memang tidak
    // ditampilkan sebagai input yang bisa diketik di UI, tapi tetap ada di
    // HTML sebagai <input type="hidden"> — bisa diubah lewat DevTools atau
    // intercepting proxy sebelum form disubmit.
    $price = (int) ($_POST['price'] ?? 0);

    $stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if ($product) {
        $note = 'Checkout normal, harga sesuai katalog.';
        if ($price < (int) $product['price']) {
            $note = 'PERINGATAN: harga yang dibayar (' . $price . ') lebih rendah dari harga katalog ('
                . $product['price'] . '). ' . FLAG_BIZ_LOGIC;
        }
        $stmt = $db->prepare(
            "INSERT INTO orders (user_id, product_id, price_paid, internal_note, created_at) VALUES (?, ?, ?, ?, datetime('now'))"
        );
        $stmt->execute([$user['id'], $productId, $price, $note]);
        $_SESSION['cart'] = [];
        header('Location: order.php?id=' . $db->lastInsertId());
        exit;
    }
}

$products = $db->query('SELECT * FROM products ORDER BY id')->fetchAll();
$pageTitle = 'Checkout';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card">
  <h1>Checkout Manual</h1>
  <p class="muted">Pilih produk yang mau dibeli sekarang juga (di luar keranjang), harga sudah otomatis mengikuti katalog.</p>
  <?php foreach ($products as $p): ?>
    <form method="post" style="margin-bottom:10px; display:flex; gap:10px; align-items:center;">
      <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
      <input type="hidden" name="price" value="<?= (int) $p['price'] ?>">
      <span style="flex:1;"><?= htmlspecialchars($p['name']) ?></span>
      <span class="price"><?= money((int) $p['price']) ?></span>
      <button type="submit">Beli Sekarang</button>
    </form>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
