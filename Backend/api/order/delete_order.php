<?php
// Admin-only delete order
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
$orderId = intval($input['id'] ?? 0);

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order id']);
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    
    // Delete order_items first (foreign key)
    $stmt = $pdo->prepare('DELETE FROM order_items WHERE order_id = ?');
    $stmt->execute([$orderId]);
    
    // Delete order
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ?');
    $stmt->execute([$orderId]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Order deleted']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
