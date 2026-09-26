<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    if ($u === '' || $p === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = get_db()->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->execute([$u, $p, 'user']);
        header('Location: login.php');
        exit;
    }
}

$pageTitle = 'Daftar';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card" style="max-width:380px;">
  <h1>Daftar Akun</h1>
  <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label>
    <input type="text" name="username">
    <label>Password</label>
    <input type="password" name="password">
    <br><br>
    <button type="submit">Daftar</button>
  </form>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
