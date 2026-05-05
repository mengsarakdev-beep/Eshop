<?php
// Admin-only user deletion endpoint
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 0);
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

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = intval($input['id'] ?? 0);

if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user id']);
    exit;
}

// Prevent self-deletion
if ($userId === intval($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete current admin user']);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$userId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'User deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
