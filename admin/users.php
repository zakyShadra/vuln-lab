<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (($_COOKIE['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Akses ditolak.');
}

$users = get_db()->query('SELECT id, username, password, role FROM users ORDER BY id')->fetchAll();

$pageTitle = 'Kelola Pengguna';
require __DIR__ . '/../includes/layout_top.php';
?>
<div class="card">
  <h1>Daftar Pengguna</h1>
  <p class="muted">Panel ini menampilkan password dalam bentuk plaintext — pelajaran tambahan soal kenapa password mestinya di-hash, bukan disimpan apa adanya.</p>
  <table>
    <tr><th>ID</th><th>Username</th><th>Password</th><th>Role</th></tr>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int) $u['id'] ?></td>
        <td><?= htmlspecialchars($u['username']) ?></td>
        <td><?= htmlspecialchars($u['password']) ?></td>
        <td><?= htmlspecialchars($u['role']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
<?php require __DIR__ . '/../includes/layout_bottom.php'; ?>
