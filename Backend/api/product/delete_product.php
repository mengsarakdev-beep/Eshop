<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../src/config/db.php';

function deleteImageFile(?string $imageUrl): void
{
    if (empty($imageUrl)) {
        return;
    }

    $path = parse_url($imageUrl, PHP_URL_PATH);
    $filename = basename($path ?: $imageUrl);

    if ($filename === 'default.png') {
        return;
    }

    $fullPath = __DIR__ . '/../../uploads/' . $filename;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtExtra = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmtExtra->execute([$id]);
    $extraImages = $stmtExtra->fetchAll(PDO::FETCH_COLUMN);

    if ($product) {
        deleteImageFile($product['image'] ?? null);
    }

    foreach ($extraImages as $extraImage) {
        deleteImageFile($extraImage);
    }

    $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>