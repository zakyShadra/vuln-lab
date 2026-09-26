<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$user = current_user();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    // VULNERABLE: tidak ada validasi ekstensi maupun isi file sama sekali.
    // Siapa pun yang login bisa upload file .php dan langsung
    // mengeksekusinya dari folder uploads/.
    $filename = basename($_FILES['avatar']['name']);
    $target = __DIR__ . '/uploads/' . uniqid() . '_' . $filename;
    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target)) {
        $publicPath = 'uploads/' . rawurlencode(basename($target));
        $message = 'File berhasil diupload: <a href="' . htmlspecialchars($publicPath) . '">'
            . htmlspecialchars(basename($target)) . '</a>';
    } else {
        $message = 'Upload gagal.';
    }
}

$pageTitle = 'Profil';
require __DIR__ . '/includes/layout_top.php';
?>
<div class="card">
  <h1>Profil: <?= htmlspecialchars($user['username']) ?></h1>
  <p class="muted">Role: <?= htmlspecialchars($user['role']) ?></p>

  <h3>Upload Foto Profil</h3>
  <?php if ($message): ?><p><?= $message ?></p><?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="avatar">
    <br><br>
    <button type="submit">Upload</button>
  </form>
</div>
<?php require __DIR__ . '/includes/layout_bottom.php'; ?>
