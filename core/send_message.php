<?php
require_once 'dbConfig.php';

// Get and validate data
$message = trim($_POST['message'] ?? '');
$document_id = isset($_POST['document_id']) ? (int) $_POST['document_id'] : 0;
$user_id = isset($_SESSION['users_id']) ? (int) $_SESSION['users_id'] : 0;


if (!$message || !$document_id || !$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'Missing data.']);
    http_response_code(400);
    exit;
}

// Check if user is the document owner or a shared editor
$accessStmt = $pdo->prepare("
    SELECT EXISTS (
        SELECT 1 FROM documents 
        WHERE documents_id = :doc_id AND owner_id = :user_id
    ) OR EXISTS (
        SELECT 1 FROM document_editors 
        WHERE document_id = :doc_id AND user_id = :user_id
    ) AS has_access
");
$accessStmt->execute([
    ':doc_id' => $document_id,
    ':user_id' => $user_id
]);

$hasAccess = $accessStmt->fetchColumn();

if (!$hasAccess) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. from send_message.php']);
    exit;
}


try {
    // Insert message
    $stmt = $pdo->prepare("INSERT INTO document_messages (document_id, user_id, message) VALUES (:document_id, :user_id, :message)");
    $stmt->execute([
        ':document_id' => $document_id,
        ':user_id' => $user_id,
        ':message' => $message
    ]);

    // Get sender's name
    $userStmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE users_id = :id");
    $userStmt->execute([':id' => $user_id]);
    $user = $userStmt->fetch();

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'user_id' => $user_id,
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}

?>