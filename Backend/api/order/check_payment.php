<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$order_id = intval($data['order_id'] ?? 0);
$transaction_id = trim((string)($data['transaction_id'] ?? ''));
$confirm = !empty($data['confirm']);

if ($order_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Order ID is required',
        'status' => 'pending'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("SELECT id, total_amount, created_at FROM orders WHERE id = ? LIMIT 1");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found',
            'status' => 'pending'
        ]);
        exit;
    }

    $status = $confirm ? 'paid' : 'pending';

    echo json_encode([
        'success' => true,
        'message' => $confirm ? 'Payment successful' : 'Order found. Awaiting payment confirmation.',
        'status' => $status,
        'order_id' => (int)$order['id'],
        'transaction_id' => $transaction_id,
        'total' => (float)$order['total_amount'],
        'created_at' => $order['created_at'],
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'status' => 'pending'
    ]);
}