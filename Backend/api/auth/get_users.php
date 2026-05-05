<?php
// Requires Admin session, returns user list
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 0);
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Not logged in', 'users' => []]);
    exit;
}

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized', 'users' => []]);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT id, username, email, phone, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'users' => $users]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage(), 'users' => []]);
}
