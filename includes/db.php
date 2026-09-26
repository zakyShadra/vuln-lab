<?php
require_once __DIR__ . '/../config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $isNew = !file_exists(DB_PATH);
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($isNew) {
        seed_db($pdo);
    }
    return $pdo;
}

function seed_db(PDO $pdo): void {
    $pdo->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL DEFAULT 'user'
    )");

    $pdo->exec("CREATE TABLE products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT,
        price INTEGER NOT NULL
    )");

    $pdo->exec("CREATE TABLE reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        username TEXT NOT NULL,
        comment TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        price_paid INTEGER NOT NULL,
        internal_note TEXT,
        created_at TEXT NOT NULL
    )");

    // Tabel ini cuma ada supaya UNION-based SQLi di search.php punya
    // sesuatu yang bernilai untuk diekstrak.
    $pdo->exec("CREATE TABLE secrets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        label TEXT NOT NULL,
        secret_value TEXT NOT NULL
    )");

    // Password disimpan plaintext dengan sengaja — memudahkan verifikasi
    // hasil SQL injection tanpa perlu crack hash.
    $pdo->exec("INSERT INTO users (username, password, role) VALUES
        ('admin', 'AdminNusantara2024!', 'admin'),
        ('alice', 'alice123', 'user'),
        ('bob', 'bob12345', 'user')");

    $pdo->exec("INSERT INTO products (name, description, price) VALUES
        ('Kabel HDMI 2 Meter', 'Kabel HDMI high speed, mendukung resolusi 4K.', 45000),
        ('Mouse Wireless Silent Click', 'Mouse wireless dengan klik senyap, baterai tahan lama.', 120000),
        ('Keyboard Mekanik TKL', 'Keyboard mekanik ukuran tenkeyless, switch blue.', 450000),
        ('Laptop Stand Aluminium', 'Stand laptop aluminium, sudut bisa diatur.', 89000)");

    $stmt = $pdo->prepare(
        "INSERT INTO reviews (product_id, username, comment, created_at) VALUES (?, ?, ?, datetime('now'))"
    );
    $stmt->execute([1, 'alice', 'Kualitasnya bagus, pengiriman cepat.']);
    $stmt->execute([2, 'bob', 'Mouse-nya nyaman dipakai kerja seharian.']);

    $stmt = $pdo->prepare(
        "INSERT INTO orders (user_id, product_id, price_paid, internal_note, created_at) VALUES (?, ?, ?, ?, datetime('now'))"
    );
    // Order #1 milik alice (user_id 2) — catatan biasa.
    $stmt->execute([2, 1, 45000, 'Catatan internal CS: pengiriman reguler, tidak ada kendala.']);
    // Order #2 milik bob (user_id 3) — ini yang jadi target latihan IDOR.
    $stmt->execute([3, 3, 450000, 'Catatan internal CS untuk order ini: ' . FLAG_IDOR]);

    $stmt = $pdo->prepare("INSERT INTO secrets (label, secret_value) VALUES (?, ?)");
    $stmt->execute(['admin_master_key', FLAG_SQLI_UNION]);
}
