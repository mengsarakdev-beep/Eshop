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

    // Get search keyword
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Optional category filter
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

    // Base SQL
    $sql = "
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
        WHERE 1
    ";

    $params = [];

    // Search by name
    if (!empty($search)) {
        $sql .= " AND p.name LIKE ?";
        $params[] = "%$search%";
    }

    // Filter by category
    if (!empty($category_id)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }

    $sql .= " ORDER BY p.id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Base URL for images
    $baseUrl = 'http://localhost/Eshop/Backend/uploads/';

    foreach ($products as &$product) {
        $product['price'] = (float) ($product['price'] ?? 0);
        $product['stock'] = isset($product['stock']) ? (int) $product['stock'] : 0;

        if (empty($product['image'])) {
            $product['image'] = $baseUrl . 'default.png';
        } elseif (!str_starts_with($product['image'], 'http')) {
            $product['image'] = $baseUrl . ltrim($product['image'], '/');
        }
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