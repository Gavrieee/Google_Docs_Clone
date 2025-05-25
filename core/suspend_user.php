<?php
require 'dbConfig.php';

// Ensure only admin can do this
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$userId = (int) $_POST['user_id'];
$suspended = (bool) $_POST['suspended'];

// $userId = 1;
// $suspended = 1;

// Prevent admins from suspending other admins
$stmt = $pdo->prepare("SELECT role FROM users WHERE users_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user || $user['role'] === 'admin') {
    exit('Cannot suspend admin');
}

// Insert or update suspension
$stmt = $pdo->prepare("
    INSERT INTO suspended_accounts (user_id, suspended)
    VALUES (?, ?)
    ON DUPLICATE KEY UPDATE suspended = VALUES(suspended)
");
$stmt->execute([$userId, $suspended]);