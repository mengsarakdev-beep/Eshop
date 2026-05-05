<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

if (!isset($_SESSION['user_id']) || (int) $_SESSION['user_id'] <= 0) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

function removeOldProfileImage(string $imageValue): void
{
    if (
        $imageValue === '' ||
        (strpos($imageValue, '/uploads/users/') === false && strpos($imageValue, 'uploads/users/') !== 0)
    ) {
        return;
    }

    $backendRoot = realpath(__DIR__ . '/../../');
    if (!$backendRoot) {
        return;
    }

    $relativePath = str_replace('http://localhost/Eshop/Backend/', '', $imageValue);
    $relativePath = ltrim($relativePath, '/');
    $fullPath = $backendRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
    $directoryPath = realpath(dirname($fullPath));

    if ($directoryPath && strpos($directoryPath, $backendRoot) === 0 && is_file($fullPath)) {
        @unlink($fullPath);
    }
}

$userId = (int) $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($username === '' || $email === '' || $phone === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Username, email, and phone are required'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();

    $currentStmt = $pdo->prepare('SELECT image FROM users WHERE id = ? LIMIT 1');
    $currentStmt->execute([$userId]);
    $currentUser = $currentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    $conflictStmt = $pdo->prepare('SELECT id FROM users WHERE (email = ? OR phone = ?) AND id <> ? LIMIT 1');
    $conflictStmt->execute([$email, $phone, $userId]);
    if ($conflictStmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email or phone is already in use'
        ]);
        exit;
    }

    $newImage = $currentUser['image'] ?? '';
    $uploadedFilePath = '';
    $previousImage = $newImage;

    if (isset($_FILES['image']) && ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if (($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload image'
            ]);
            exit;
        }

        $tmpFile = $_FILES['image']['tmp_name'];
        $fileSize = (int) ($_FILES['image']['size'] ?? 0);

        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode([
                'success' => false,
                'message' => 'Image size must be 5MB or less'
            ]);
            exit;
        }

        $allowedMimeTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $tmpFile) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!$mimeType || !isset($allowedMimeTypes[$mimeType])) {
            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, PNG, GIF, and WEBP images are allowed'
            ]);
            exit;
        }

        $uploadDir = __DIR__ . '/../../uploads/users/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            echo json_encode([
                'success' => false,
                'message' => 'Unable to create upload folder'
            ]);
            exit;
        }

        $extension = $allowedMimeTypes[$mimeType];
        $filename = 'user_' . $userId . '_' . uniqid('', true) . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($tmpFile, $targetPath)) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to save uploaded image'
            ]);
            exit;
        }

        $uploadedFilePath = $targetPath;
        $newImage = 'http://localhost/Eshop/Backend/uploads/users/' . $filename;
    }

    $updateStmt = $pdo->prepare('UPDATE users SET username = ?, email = ?, phone = ?, image = ? WHERE id = ?');
    $updateStmt->execute([$username, $email, $phone, $newImage, $userId]);

    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;
    $_SESSION['image'] = $newImage;

    if ($uploadedFilePath !== '' && $previousImage !== '' && $previousImage !== $newImage) {
        removeOldProfileImage($previousImage);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully',
        'user' => [
            'id' => $userId,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'role' => $_SESSION['role'] ?? 'user',
            'image' => $newImage
        ]
    ]);
} catch (PDOException $e) {
    if ($uploadedFilePath !== '' && is_file($uploadedFilePath)) {
        @unlink($uploadedFilePath);
    }

    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
