<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$db = get_db();
$stmt = $db->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Tidak ditemukan';
    require __DIR__ . '/includes/layout_top.php';
    echo '<div class="card"><p>Produk tidak ditemukan.</p></div>';
    require __DIR__ . '/includes/layout_bottom.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $user = current_user();
    $username = $user ? $user['username'] : 'Anonim';
    $comment = $_POST['comment'];
    // VULNERABLE: komentar disimpan apa adanya. Tidak berbahaya di titik ini
    // — bahayanya ada saat DITAMPILKAN tanpa escaping (lihat bawah).
    $stmt = $db->prepare("INSERT INTO reviews (product_id, username, comment, created_at) VALUES (?, ?, ?, datetime('now'))");
    $stmt->execute([$id, $username, $comment]);
    header('Location: product.php?id=' . $id);
    exit;
}

$reviewStmt = $db->prepare('SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC');
$reviewStmt->execute([$id]);
$reviews = $reviewStmt->fetchAll();

$pageTitle = $product['name'];
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card">
  <h1><?= htmlspecialchars($product['name']) ?></h1>
  <p class="price"><?= money((int) $product['price']) ?></p>
  <p><?= htmlspecialchars($product['description']) ?></p>
  <form method="post" action="cart.php">
    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
    <button type="submit">Tambah ke Keranjang</button>
  </form>
</div>

<div class="card">
  <h2>Ulasan</h2>
  <?php foreach ($reviews as $r): ?>
    <div class="review">
      <strong><?= htmlspecialchars($r['username']) ?></strong>
      <div><?= $r['comment'] /* VULNERABLE: sengaja tidak di-escape — stored XSS */ ?></div>
    </div>
  <?php endforeach; ?>
  <?php if (!$reviews): ?><p class="muted">Belum ada ulasan.</p><?php endif; ?>

  <h3>Tulis Ulasan</h3>
  <form method="post">
    <textarea name="comment" rows="3" placeholder="Tulis ulasanmu..."></textarea>
    <br><br>
    <button type="submit">Kirim Ulasan</button>
  </form>
</div>

<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
