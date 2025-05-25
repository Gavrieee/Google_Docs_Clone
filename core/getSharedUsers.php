<?php
require_once 'dbConfig.php';

if (isset($_GET['document_id'])) {
    $documentId = $_GET['document_id'];

    $stmt = $pdo->prepare("
        SELECT users.users_id, users.first_name, users.last_name, users.username 
        FROM document_editors
        JOIN users ON document_editors.user_id = users.users_id
        WHERE document_editors.document_id = ?
    ");
    $stmt->execute([$documentId]);
    $sharedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sharedUsers);
    exit;
}