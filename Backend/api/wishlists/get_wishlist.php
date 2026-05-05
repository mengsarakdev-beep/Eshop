<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../../api/src/config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'products' => [],
        'wishlists' => []
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("
        SELECT w.id, p.id AS product_id, p.name AS product_name, w.created_at
        FROM wishlists w
        JOIN products p ON p.id = w.product_id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$user_id]);

    $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $productIds = array_values(array_unique(array_map(static fn($item) => (int) $item['product_id'], $wishlists)));

    echo json_encode([
        'success' => true,
        'products' => $productIds,
        'wishlists' => $wishlists,
        'count' => count($productIds)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'products' => [],
        'wishlists' => []
    ]);
}
