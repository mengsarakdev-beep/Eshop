<?php
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 0); // set 1 if using HTTPS
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../../api/src/config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare('DELETE FROM wishlists WHERE user_id = ?');
    $stmt->execute([intval($_SESSION['user_id'])]);

    echo json_encode([
        'success' => true,
        'message' => 'Wishlist cleared'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
