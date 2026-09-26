<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
<style>
  * { box-sizing: border-box; }
  body { margin: 0; font-family: -apple-system, Segoe UI, Roboto, sans-serif; background: #f4f5f7; color: #1a1d23; }
  a { color: #1a6b3c; }
  header.site { background: #1a6b3c; color: #fff; padding: 14px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
  header.site a { color: #fff; text-decoration: none; }
  header.site .brand { font-weight: 700; font-size: 1.2rem; }
  header.site nav { display: flex; gap: 16px; align-items: center; font-size: 0.92rem; }
  main { max-width: 960px; margin: 0 auto; padding: 24px; }
  .card { background: #fff; border: 1px solid #e2e4e8; border-radius: 8px; padding: 18px 20px; margin-bottom: 16px; }
  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }
  .product { background: #fff; border: 1px solid #e2e4e8; border-radius: 8px; padding: 16px; }
  .product h3 { margin: 0 0 6px; font-size: 1rem; }
  .price { color: #1a6b3c; font-weight: 700; }
  form.inline { display: flex; gap: 8px; }
  input, textarea, select { font: inherit; padding: 8px 10px; border: 1px solid #ccd0d6; border-radius: 6px; width: 100%; }
  label { display: block; font-size: 0.85rem; color: #555; margin: 10px 0 4px; }
  button, .btn { background: #1a6b3c; color: #fff; border: none; border-radius: 6px; padding: 9px 16px; cursor: pointer; font-size: 0.92rem; text-decoration: none; display: inline-block; }
  button:hover, .btn:hover { background: #145030; }
  .flag-banner { background: #103a1f; color: #7ed957; border: 1px solid #2f6b45; border-radius: 8px; padding: 14px 16px; margin-bottom: 16px; font-family: monospace; font-size: 0.95rem; }
  .muted { color: #6b7280; font-size: 0.88rem; }
  table { width: 100%; border-collapse: collapse; }
  table th, table td { text-align: left; padding: 8px; border-bottom: 1px solid #e2e4e8; font-size: 0.9rem; }
  .review { border-top: 1px solid #eee; padding: 8px 0; }
  .error { color: #b42318; }
</style>
</head>
<body>
<header class="site">
  <a class="brand" href="/index.php"><?= APP_NAME ?></a>
  <nav>
    <a href="/index.php">Katalog</a>
    <form class="inline" action="/search.php" method="get" style="width:auto;">
      <input type="text" name="q" placeholder="Cari produk..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
    </form>
    <a href="/cart.php">Keranjang</a>
    <?php if ($user): ?>
      <a href="/profile.php">Profil (<?= htmlspecialchars($user['username']) ?>)</a>
      <?php if ($user['role'] === 'admin'): ?><a href="/admin/index.php">Admin</a><?php endif; ?>
      <a href="/logout.php">Keluar</a>
    <?php else: ?>
      <a href="/login.php">Masuk</a>
      <a href="/register.php">Daftar</a>
    <?php endif; ?>
  </nav>
</header>
<main>
