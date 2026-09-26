<?php
function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function money(int $amount): string {
    return 'Rp' . number_format($amount, 0, ',', '.');
}
