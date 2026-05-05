<?php
// ---------------------- SESSION & COOKIE CONFIG ----------------------
ini_set('session.cookie_samesite', 'None'); // allow cross-origin cookies
ini_set('session.cookie_secure', 0); // set 1 if using HTTPS
session_start();

// ---------------------- HEADERS ----------------------
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173'); // frontend URL
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// ---------------------- DATABASE ----------------------
require_once __DIR__ . '/../../api/src/config/db.php';

// ---------------------- SESSION CHECK ----------------------
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in'
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// ---------------------- READ INPUT ----------------------
$input = json_decode(file_get_contents('php://input'), true);
$wishlist_id = intval($input['id'] ?? 0);
$product_id = intval($input['product_id'] ?? 0);
$user_id_from_request = intval($input['user_id'] ?? 0);

try {
    $pdo = Database::getInstance();

    if ($wishlist_id) {
        // Admin or specific row removal by wishlist ID
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE id = ?");
        $stmt->execute([$wishlist_id]);
    } elseif ($product_id) {
        // user-based removal
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    } elseif ($user_id_from_request && $product_id) {
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id_from_request, $product_id]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Wishlist ID or Product ID required'
        ]);
        exit;
    }

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Wishlist item not found'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Removed from wishlist'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}