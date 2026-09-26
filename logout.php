<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['user']);
setcookie('role', '', time() - 3600, '/');
header('Location: index.php');
