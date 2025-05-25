<?php

require_once 'dbConfig.php';

function removeSharedUser($pdo, $documentId, $userIdToRemove)
{
    $sql = "DELETE FROM document_editors WHERE document_id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$documentId, $userIdToRemove]);
}


function checkIfUserExists($pdo, $username)
{
    $response = array();

    $sql = "SELECT * FROM users WHERE username = ?";

    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$username])) {
        $userInfoArray = $stmt->fetch();

        if ($stmt->rowCount() > 0) {
            $response = array(
                "result" => true,
                "status" => "200",
                "userInfoArray" => $userInfoArray
            );
        } else {
            $response = array(
                "result" => false,
                "status" => "400",
                "message" => "User doesn't exist from the database"
            );
        }
    }

    return $response;
}

function insertNewUser($pdo, $first_name, $last_name, $username, $password, $role)
{
    $response = array();

    $checkIfUserExists = checkIfUserExists($pdo, $username);

    if (!$checkIfUserExists['result']) {
        $sql = "INSERT INTO users (first_name, last_name, username, password, role) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        // Insert default role as 'user'
        if ($stmt->execute([$first_name, $last_name, $username, $password, $role])) {
            $response = array(
                "status" => "200",
                "message" => "User successfully inserted!"
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "An error occurred with the query!"
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "User already exists!"
        );
    }

    return $response;
}

function getAllUsers($pdo)
{
    $sql = "SELECT * FROM users";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();

    if ($executeQuery) {
        return $stmt->fetchAll();
    }
}

function insertNewDocument($pdo, $title, $content, $user_id)
{
    $response = array();

    $sql = "INSERT INTO documents (title, content, owner_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$title, $content, $user_id])) {
        $response = array(
            "status" => "200",
            "message" => "Document successfully created!"
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "An error occurred with the query!"
        );
    }
    return $response;
}

function getAllDocuments($pdo)
{
    $sql = "SELECT * FROM documents";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute();

    if ($executeQuery) {
        return $stmt->fetchAll();
    }
}

function showAllDocumentsPerUser($pdo, $user_id)
{
    // Get role of the user
    $roleStmt = $pdo->prepare("SELECT role FROM users WHERE users_id = ?");
    $roleStmt->execute([$user_id]);
    $user = $roleStmt->fetch(PDO::FETCH_ASSOC);

    // If admin, return all documents
    if ($user && $user['role'] === 'admin') {
        $sql = "
            SELECT d.*, 'admin' AS role
            FROM documents d
        ";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Otherwise, show owned and editable documents
    $sql = "
        SELECT d.*, 'owner' AS role
        FROM documents d
        WHERE d.owner_id = :user_id

        UNION

        SELECT d.*, 'editor' AS role
        FROM documents d
        INNER JOIN document_editors de ON d.documents_id = de.document_id
        WHERE de.user_id = :user_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getDocumentById($pdo, $document_id)
{
    $sql = "SELECT * FROM documents WHERE documents_id = ?";
    $stmt = $pdo->prepare($sql);
    $executeQuery = $stmt->execute([$document_id]);

    if ($executeQuery) {
        return $stmt->fetch();
    }
}

function showContentPerDocument($pdo, $document_id, $user_id)
{
    $sql = "
        SELECT d.content
        FROM documents d
        WHERE d.documents_id = :doc_id AND d.owner_id = :user_id

        UNION

        SELECT d.content
        FROM documents d
        INNER JOIN document_editors de ON d.documents_id = de.document_id
        WHERE d.documents_id = :doc_id AND de.user_id = :user_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':doc_id' => $document_id,
        ':user_id' => $user_id
    ]);

    $document = $stmt->fetch();

    return $document ? $document['content'] : "";
}


function checkIfTitleExists($pdo, $title, $user_id)
{
    $sql = "SELECT 1 FROM documents WHERE title = ? AND owner_id = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$title, $user_id]);

    return $stmt->fetchColumn() !== false;
}

function searchUsersToShare($pdo, $searchTerm, $currentUserId, $documentId)
{
    $sql = "
        SELECT users_id, first_name, last_name, username
        FROM users
        WHERE (
            first_name LIKE :search OR
            last_name LIKE :search OR
            username LIKE :search
        )
        AND users_id != :currentUser
        AND users_id NOT IN (
            SELECT user_id FROM document_editors WHERE document_id = :docId
        )
    ";

    try {
        $stmt = $pdo->prepare($sql);

        $likeSearch = "%$searchTerm%";

        $stmt->bindValue(':search', $likeSearch);
        $stmt->bindValue(':currentUser', $currentUserId, PDO::PARAM_INT);
        $stmt->bindValue(':docId', $documentId, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        return ['error' => 'PDO Error: ' . $e->getMessage()];
    }
}

function getUsersWithAccess($pdo, $documentId)
{
    $sql = "SELECT u.users_id, u.first_name, u.last_name, u.username
            FROM document_editors de
            JOIN users u ON de.user_id = u.users_id
            WHERE de.document_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$documentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDocumentMessages($pdo, $documentId)
{
    $sql = "SELECT dm.message, dm.timestamp, u.first_name, u.last_name, u.username 
            FROM document_messages dm
            JOIN users u ON dm.user_id = u.users_id
            WHERE dm.document_id = :documentId
            ORDER BY dm.timestamp ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['documentId' => $documentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function hasDocumentAccess($pdo, $document_id, $user_id)
{
    $sql = "
        SELECT 1 FROM documents 
        WHERE documents_id = :doc_id AND owner_id = :user_id
        UNION
        SELECT 1 FROM document_editors 
        WHERE document_id = :doc_id AND user_id = :user_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':doc_id' => $document_id,
        ':user_id' => $user_id
    ]);

    return $stmt->fetchColumn() ? true : false;
}

function getDocumentDetails($pdo, $documentId)
{
    $stmt = $pdo->prepare("SELECT * FROM documents WHERE documents_id = ?");
    $stmt->execute([$documentId]);
    return $stmt->fetch();
}

function logActivity(PDO $pdo, int $documentId, int $userId, string $action): bool
{
    $sql = "
        INSERT INTO activity_logs (document_id, user_id, action)
        VALUES (:document_id, :user_id, :action)
    ";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        'document_id' => $documentId,
        'user_id' => $userId,
        'action' => $action
    ]);
}

function getActivityLogsByDocumentId(PDO $pdo, int $documentId): array
{
    $sql = "
        SELECT a.action, a.timestamp, u.username AS username
        FROM activity_logs a
        JOIN users u ON a.user_id = u.users_id
        WHERE a.document_id = :document_id
        ORDER BY a.timestamp DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['document_id' => $documentId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getAllNonAdminUsers($pdo)
{
    $sql = "
        SELECT u.users_id, u.first_name, u.last_name, u.username, sa.suspended
        FROM users u
        LEFT JOIN suspended_accounts sa ON u.users_id = sa.user_id
        WHERE u.role != 'admin'
        ORDER BY u.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}