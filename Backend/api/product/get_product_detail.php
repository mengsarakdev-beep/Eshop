<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../src/config/db.php';

$id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);
} else {
    $id = intval($_GET['id'] ?? 0);
}

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $baseUrl = 'http://localhost/Eshop/Backend/uploads/';
    $normalizeImageUrl = function ($img) use ($baseUrl) {
        if (empty($img)) {
            return $baseUrl . 'default.png';
        }

        return str_starts_with($img, 'http://') || str_starts_with($img, 'https://')
            ? $img
            : $baseUrl . ltrim($img, '/');
    };

    // Fetch main product info
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.description, p.price, p.stock, p.category_id, p.image AS main_image, p.created_at,
               c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    $images = [];

    // Fetch additional images
    $stmt2 = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt2->execute([$id]);
    $images = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    // Ensure main image is included
    if (!empty($product['main_image']) && !in_array($product['main_image'], $images, true)) {
        array_unshift($images, $product['main_image']);
    }

    // Fallback if no images exist
    if (empty($images)) {
        $images[] = 'default.png';
    }

    $images = array_values(array_unique(array_map($normalizeImageUrl, $images)));

    // Match Vue expectation
    $product['image'] = $normalizeImageUrl($product['main_image'] ?? ($images[0] ?? null));
    $product['images'] = $images;
    unset($product['main_image']);

    echo json_encode(['success' => true, 'product' => $product]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}