<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 0); // set 1 if using HTTPS
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

require_once __DIR__ . '/../../api/src/config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'products' => []
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.price, p.image, p.stock
        FROM wishlists w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.id DESC
    ");
    $stmt->execute([$user_id]);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'products' => []
    ]);
}