<?php
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['action']) && $_POST['action'] === 'shareDocument') {
    $userId = $_POST['user_id'];
    $documentId = $_POST['document_id'];

    try {
        $stmt = $pdo->prepare("INSERT INTO document_editors (document_id, user_id) VALUES (?, ?)");
        $stmt->execute([$documentId, $userId]);

        echo json_encode(["status" => "200", "message" => "Document successfully shared."]);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(["status" => "409", "message" => "This user already has access."]);
        } else {
            echo json_encode(["status" => "500", "message" => "Something went wrong."]);
        }
    }
    exit;
}

if (isset($_POST['action']) && $_POST['action'] === 'revokeAccess') {
    $userId = $_POST['user_id'];
    $documentId = $_POST['document_id'];

    $stmt = $pdo->prepare("DELETE FROM document_editors WHERE document_id = ? AND user_id = ?");
    $stmt->execute([$documentId, $userId]);

    echo json_encode(["status" => "200", "message" => "Access revoked successfully."]);
    exit;
}



if (isset($_POST['getUsersWithAccess'])) {
    if (!is_ajax_request()) {
        echo json_encode(["status" => "403", "message" => "Forbidden"]);
        exit;
    }

    $documentId = $_POST['document_id'] ?? null;

    if (!$documentId) {
        echo json_encode(["status" => "400", "message" => "Missing document ID"]);
        exit;
    }

    $users = getUsersWithAccess($pdo, $documentId);

    echo json_encode(["status" => "200", "users" => $users]);
    exit;
}


if (isset($_POST['shareDocument'])) {
    if (!is_ajax_request()) {
        echo json_encode(["status" => "403", "message" => "Forbidden"]);
        exit;
    }

    $userId = $_POST['user_id'] ?? null;
    $documentId = $_POST['document_id'] ?? null;

    if ($userId && $documentId) {
        // Check if already shared
        $stmt = $pdo->prepare("SELECT * FROM document_editors WHERE user_id = ? AND document_id = ?");
        $stmt->execute([$userId, $documentId]);
        if ($stmt->rowCount() > 0) {
            echo "Document already shared with this user.";
            exit;
        }

        // Insert sharing record
        $stmt = $pdo->prepare("INSERT INTO document_editors (document_id, user_id) VALUES (?, ?)");
        if ($stmt->execute([$documentId, $userId])) {
            echo "Document successfully shared!";
        } else {
            echo "Failed to share document.";
        }
    } else {
        echo "Missing user or document ID.";
    }
    exit;
}


function is_ajax_request()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// if (!isset($_SESSION['username'])) {
//     if (is_ajax_request()) {
//         echo json_encode(["status" => "401", "message" => "Unauthorized"]);
//         exit;
//     } else {
//         header("Location: ../main/index.php");
//         exit;
//     }
// }

if (isset($_POST['registerButton'])) {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $is_admin = isset($_POST['is_admin']) ? 'admin' : 'user';

    if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {
        if ($password == $confirm_password) {
            $insertQuery = insertNewUser($pdo, $first_name, $last_name, $username, password_hash($password, PASSWORD_DEFAULT), $is_admin);

            $_SESSION['message'] = $insertQuery['message'];

            if ($insertQuery['status'] == '200') {
                $_SESSION['message'] = $insertQuery['message'];
                $_SESSION['status'] = $insertQuery['status'];
                header("Location: ../pages/authentication/login.php");
            } else {
                $_SESSION['message'] = $insertQuery['message'];
                $_SESSION['status'] = $insertQuery['status'];
                header("Location: ../pages/authentication/register.php");
            }
        } else {
            $_SESSION['message'] = "Please make sure both passwords are equal";
            $_SESSION['status'] = '400';
            header("Location: ../pages/authentication/register.php");
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../pages/authentication/register.php");
    }
}

if (isset($_POST['loginButton'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $loginQuery = checkIfUserExists($pdo, $username);

        if ($loginQuery && isset($loginQuery['userInfoArray'])) {
            $userIDFromDB = $loginQuery['userInfoArray']['users_id'];
            $usernameFromDB = $loginQuery['userInfoArray']['username'];
            $passwordFromDB = $loginQuery['userInfoArray']['password'];
            $userRoleFromDB = $loginQuery['userInfoArray']['role'];

            // Check if the user is suspended
            $suspendCheck = $pdo->prepare("SELECT suspended FROM suspended_accounts WHERE user_id = :user_id");
            $suspendCheck->execute([':user_id' => $userIDFromDB]);
            $suspendData = $suspendCheck->fetch(PDO::FETCH_ASSOC);

            if ($suspendData && $suspendData['suspended']) {
                $_SESSION['message'] = "Your account has been suspended. Please contact an administrator.";
                $_SESSION['status'] = '403';
                header("Location: ../pages/authentication/login.php");
                exit;
            }

            // Check password
            if (password_verify($password, $passwordFromDB)) {
                $_SESSION['users_id'] = $userIDFromDB;
                $_SESSION['username'] = $usernameFromDB;
                $_SESSION['role'] = $userRoleFromDB;
                header("Location: ../pages/main/index.php");
                exit;
            } else {
                $_SESSION['message'] = "Username/password invalid";
                $_SESSION['status'] = "400";
                header("Location: ../pages/authentication/login.php");
                exit;
            }
        } else {
            $_SESSION['message'] = "Username not found.";
            $_SESSION['status'] = "404";
            header("Location: ../pages/authentication/login.php");
            exit;
        }
    } else {
        $_SESSION['message'] = "Please make sure there are no empty input fields";
        $_SESSION['status'] = '400';
        header("Location: ../pages/authentication/login.php");
        exit;
    }
}


if (isset($_POST['logoutButton'])) {
    // session_unset(); // Clear all session variables
    unset($_SESSION['username']);
    // session_destroy(); // End session
    header("Location: ../pages/authentication/login.php");
    exit();
}

if (isset($_POST["createDocButton"])) {

    $owner_id = $_SESSION['users_id'];
    $title = $_POST['docuName'];
    $content = null;

    if (!empty($title)) {

        $checkIfTitleExists = checkIfTitleExists($pdo, $title, $owner_id);

        if ($checkIfTitleExists) {
            $_SESSION['message'] = "Document with this title already exists!";
            $_SESSION['status'] = '400';
            header("Location: ../pages/main/index.php");
            exit();
        }

        $createDocumentQuery = insertNewDocument($pdo, $title, $content, $owner_id);

        if ($createDocumentQuery['status'] == '200') {
            $_SESSION['message'] = $createDocumentQuery['message'];
            $_SESSION['status'] = $createDocumentQuery['status'];
            header("Location: ../pages/main/index.php");
        } else {
            $_SESSION['message'] = $createDocumentQuery['message'];
            $_SESSION['status'] = $createDocumentQuery['status'];
            header("Location: ../pages/main/index.php");
        }
    }
}