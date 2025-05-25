<?php
require_once 'dbConfig.php';
require_once 'models.php';

$documentId = $_POST['doc_id'] ?? 0;
$userIdToRemove = $_POST['user_id'] ?? 0;
$username = $_POST['username'] ?? '';

// Optional: Confirm the current user has rights to modify sharing
$currentUserId = $_SESSION['users_id'] ?? 0;

if ($documentId && $userIdToRemove && $currentUserId) {
    $success = removeSharedUser($pdo, $documentId, $userIdToRemove);

    echo json_encode([
        'success' => $success,
        'message' => $success ? $username . "'s access has been revoked." : 'Failed to remove user',
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data',
    ]);
}