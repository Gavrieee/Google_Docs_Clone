<?php
header('Content-Type: application/json');
require_once 'dbConfig.php';

if (!isset($_SESSION['users_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['document_id'])) {
    echo json_encode(['success' => false, 'error' => 'DOCUMENT ID NOT FOUND']);
    exit;
}

$document_id = (int) $_POST['document_id'];
$uploaded_by = $_SESSION['users_id'];

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'Uploading Image failed']);
    exit;
}

$image = $_FILES['image'];
$ext = pathinfo($image['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $ext;
$destination = __DIR__ . '/../uploads/' . $filename;
$url = "/google_docs_clone/uploads/{$filename}";

if (!move_uploaded_file($image['tmp_name'], $destination)) {
    echo json_encode(['success' => false, 'error' => 'Failed to move image']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO document_images (document_id, file_path, uploaded_by) VALUES (?, ?, ?)");
$stmt->execute([$document_id, $url, $uploaded_by]);

echo json_encode(['success' => true, 'url' => $url]);
