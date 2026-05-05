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

if (!isset($_FILES['image']) || ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
    echo json_encode([
        'success' => false,
        'message' => 'Please select an image to upload'
    ]);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

$tmpFile = $_FILES['image']['tmp_name'];
$originalName = $_FILES['image']['name'] ?? 'profile.png';
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

$extension = $allowedMimeTypes[$mimeType];
$uploadDir = __DIR__ . '/../../uploads/users/';

if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
    echo json_encode([
        'success' => false,
        'message' => 'Unable to create upload folder'
    ]);
    exit;
}

$filename = 'user_' . (int) $_SESSION['user_id'] . '_' . uniqid('', true) . '.' . $extension;
$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($tmpFile, $targetPath)) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save uploaded image'
    ]);
    exit;
}

$imageUrl = 'http://localhost/Eshop/Backend/uploads/users/' . $filename;

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare('SELECT image FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([(int) $_SESSION['user_id']]);
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentImage = $currentUser['image'] ?? '';

    $updateStmt = $pdo->prepare('UPDATE users SET image = ? WHERE id = ?');
    $updateStmt->execute([$imageUrl, (int) $_SESSION['user_id']]);

    $_SESSION['image'] = $imageUrl;

    $uploadsRoot = realpath(__DIR__ . '/../../uploads');
    if (!empty($currentImage) && strpos($currentImage, '/uploads/users/') !== false && $uploadsRoot) {
        $oldFilePath = $uploadsRoot . DIRECTORY_SEPARATOR . str_replace(['http://localhost/Eshop/Backend/uploads/', '/'], ['', DIRECTORY_SEPARATOR], $currentImage);
        if (is_file($oldFilePath) && realpath($oldFilePath) && strpos(realpath($oldFilePath), $uploadsRoot) === 0) {
            @unlink($oldFilePath);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Profile image updated successfully',
        'image' => $imageUrl,
        'filename' => basename($originalName)
    ]);
} catch (PDOException $e) {
    if (is_file($targetPath)) {
        @unlink($targetPath);
    }

    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
