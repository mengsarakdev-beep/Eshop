<?php
// ✅ MUST be before session_start
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', '0'); // set 1 if HTTPS

session_start();

header('Content-Type: application/json');

// ✅ FIX CORS (NO *)
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../../api/src/config/db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
        'session' => $_SESSION // debug
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Read JSON
$input = json_decode(file_get_contents('php://input'), true);
$product_id = intval($input['product_id'] ?? 0);

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Product ID required']);
    exit;
}

try {
    $pdo = Database::getInstance();

    // Check duplicate
    $stmt = $pdo->prepare("SELECT id FROM wishlists WHERE user_id=? AND product_id=?");
    $stmt->execute([$user_id, $product_id]);

    if ($stmt->fetch()) {
        echo json_encode([
            'success' => true, // ✅ still success (already liked)
            'message' => 'Already in wishlist'
        ]);
        exit;
    }

    // Insert
    $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Added to wishlist'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}