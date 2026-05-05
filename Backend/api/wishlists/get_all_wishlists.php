<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("
        SELECT w.id, w.user_id, u.username AS user_name, p.id AS product_id, p.name AS product_name, 
               p.image AS product_image, p.price AS product_price, w.created_at
        FROM wishlists w
        JOIN users u ON u.id = w.user_id
        JOIN products p ON p.id = w.product_id
        ORDER BY w.created_at DESC
    ");
    $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'wishlists' => $wishlists
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}