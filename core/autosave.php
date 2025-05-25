<?php

require_once 'dbConfig.php';
require_once 'models.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$content = $_POST['content'] ?? '';
$user_id = $_SESSION['users_id'];
$documents_id = $_SESSION['documents_id'];
$action = 'made changes to the document.';

// Check if the user has access (owner or shared editor)
$accessCheckStmt = $pdo->prepare("
    SELECT 1 FROM documents 
    WHERE documents_id = ? AND owner_id = ?
    UNION
    SELECT 1 FROM document_editors 
    WHERE document_id = ? AND user_id = ?
    LIMIT 1
");
$accessCheckStmt->execute([$documents_id, $user_id, $documents_id, $user_id]);

if ($accessCheckStmt->fetchColumn()) {
    // User has access, update content
    $stmt = $pdo->prepare("UPDATE documents SET content = ? WHERE documents_id = ?");

    if (logActivity($pdo, $documents_id, $user_id, $action)) {
        echo "Activity logged.";
    } else {
        echo "Failed to log activity.";
        exit;
    }

    $stmt->execute([$content, $documents_id]);
    echo "Saved";
} else {
    http_response_code(403);
    echo "Access denied";
}