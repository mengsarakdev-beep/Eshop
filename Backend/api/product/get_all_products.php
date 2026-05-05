<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../src/config/db.php';

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

    // 1️⃣ Fetch all products
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.description, p.price, p.stock, p.category_id, p.image AS main_image
        FROM products p
        ORDER BY p.id DESC
    ");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];

    foreach ($products as $product) {
        $productId = $product['id'];

        // Fetch additional images
        $stmt2 = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
        $stmt2->execute([$productId]);
        $images = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($product['main_image']) && !in_array($product['main_image'], $images, true)) {
            array_unshift($images, $product['main_image']);
        }

        if (empty($images)) {
            $images[] = 'default.png';
        }

        $images = array_values(array_unique(array_map($normalizeImageUrl, $images)));

        $result[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => (float)$product['price'],
            'stock' => (int)$product['stock'],
            'image' => $normalizeImageUrl($product['main_image'] ?? ($images[0] ?? null)),
            'images' => $images,
            'category_id' => $product['category_id']
        ];
    }

    echo json_encode(['success' => true, 'products' => $result]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}