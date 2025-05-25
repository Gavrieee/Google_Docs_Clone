<?php
require_once 'dbConfig.php';

$document_id = $_GET['document_id'] ?? 0;
$user_id = $_SESSION['users_id'] ?? 0;

// Check access
$accessStmt = $pdo->prepare("
    SELECT 1 FROM documents WHERE documents_id = :doc_id AND owner_id = :user_id
    UNION
    SELECT 1 FROM document_editors WHERE document_id = :doc_id AND user_id = :user_id
");
$accessStmt->execute([':doc_id' => $document_id, ':user_id' => $user_id]);

if (!$accessStmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

// Fetch messages
$msgStmt = $pdo->prepare("
    SELECT m.message, m.timestamp, u.first_name, u.last_name, u.users_id
    FROM document_messages m
    JOIN users u ON m.user_id = u.users_id
    WHERE m.document_id = :doc_id
    ORDER BY m.timestamp ASC
");
$msgStmt->execute([':doc_id' => $document_id]);
$messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status' => 'success', 'messages' => $messages]);

?>