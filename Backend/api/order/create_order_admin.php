<?php
// Admin-only create order
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
$userId = intval($input['user_id'] ?? 0);
$totalAmount = floatval($input['total_amount'] ?? 0);
$address = trim($input['address'] ?? '');

if ($userId <= 0 || $totalAmount <= 0 || !$address) {
    echo json_encode(['success' => false, 'message' => 'User ID, total amount, and address required']);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    
    // Verify user exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Create order
    $stmt = $pdo->prepare('INSERT INTO orders (user_id, total_amount, address, created_at) VALUES (?, ?, ?, NOW())');
    $stmt->execute([$userId, $totalAmount, $address]);
    $orderId = $pdo->lastInsertId();

    echo json_encode(['success' => true, 'message' => 'Order created', 'order_id' => $orderId]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
