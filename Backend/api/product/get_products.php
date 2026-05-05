<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// Include database connection
require_once __DIR__ . '/../src/config/db.php';

try {
    $pdo = Database::getInstance();

    // Get category_id from request
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

    // Prepare main query
    if ($category_id) {
        $stmt = $pdo->prepare("
            SELECT 
                p.id, 
                p.name, 
                p.description, 
                p.price, 
                p.stock, 
                p.category_id, 
                p.image, 
                c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ?
            ORDER BY p.id DESC
        ");
        $stmt->execute([$category_id]);
    } else {
        $stmt = $pdo->query("
            SELECT 
                p.id, 
                p.name, 
                p.description, 
                p.price, 
                p.stock, 
                p.category_id, 
                p.image, 
                c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $baseUrl = 'http://localhost/Eshop/Backend/uploads/';

    // Process images
    foreach ($products as &$product) {
        // Fix main image
        if (empty($product['image'])) {
            $product['image'] = $baseUrl . 'default.png';
        } elseif (!str_starts_with($product['image'], 'http')) {
            $product['image'] = $baseUrl . $product['image'];
        }

        // Fetch additional images from product_images table
        $stmtImg = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
        $stmtImg->execute([$product['id']]);
        $images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

        // Fix additional images URLs and remove duplicate of main image
        $cleanImages = [];
        foreach ($images as $img) {
            if (!str_starts_with($img, 'http')) {
                $img = $baseUrl . $img;
            }
            if ($img !== $product['image']) {
                $cleanImages[] = $img;
            }
        }

        $product['images'] = $cleanImages; // Only additional images
    }

    echo json_encode([
        'success' => true,
        'products' => $products
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}