<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    // VULNERABLE: query login dibangun lewat string concatenation langsung,
    // tanpa prepared statement. Ini titik masuk SQL injection.
    $sql = "SELECT * FROM users WHERE username = '$u' AND password = '$p'";

    try {
        $row = get_db()->query($sql)->fetch();
    } catch (PDOException $e) {
        $row = false;
        $error = 'Query error: ' . $e->getMessage();
    }

    if ($row) {
        $_SESSION['user'] = ['id' => $row['id'], 'username' => $row['username'], 'role' => $row['role']];
        // Role disimpan di cookie biar admin/index.php bisa "ingat" siapa yang
        // login tanpa query ulang — tapi cookie ini bisa diubah bebas oleh
        // browser, dan admin/index.php mempercayainya begitu saja.
        setcookie('role', $row['role'], time() + 86400, '/');

        // Kalau baris yang ke-match punya password BEDA dari yang disubmit,
        // itu bukti query-nya "ditipu" lewat SQL injection (bukan berhasil
        // login pakai kredensial asli). Dipakai admin/index.php buat kasih
        // flag studi kasus SQLi secara terpisah dari flag broken-access-nya.
        if ($row['password'] !== $p) {
            $_SESSION['sqli_login_bypass'] = true;
        }

        header('Location: index.php');
        exit;
    } elseif (!$error) {
        $error = 'Username atau password salah.';
    }
}

$pageTitle = 'Masuk';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card" style="max-width:380px;">
  <h1>Masuk</h1>
  <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
  <form method="post">
    <label>Username</label>
    <input type="text" name="username">
    <label>Password</label>
    <input type="password" name="password">
    <br><br>
    <button type="submit">Masuk</button>
  </form>
  <p class="muted">Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
