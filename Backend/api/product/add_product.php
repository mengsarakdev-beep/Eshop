<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/config/db.php';

function uploadImages(array $fileInput, string $uploadDir): array
{
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $names = $fileInput['name'] ?? [];
    $tmpNames = $fileInput['tmp_name'] ?? [];
    $errors = $fileInput['error'] ?? [];

    if (!is_array($names)) {
        $names = [$names];
        $tmpNames = [$tmpNames];
        $errors = [$errors];
    }

    $uploadedImages = [];

    foreach ($names as $index => $originalName) {
        if (empty($originalName) || ($errors[$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }

        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '-', basename($originalName));
        $filename = uniqid('product_', true) . '-' . $safeName;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($tmpNames[$index], $targetPath)) {
            $uploadedImages[] = 'http://localhost/Eshop/Backend/uploads/' . $filename;
        }
    }

    return $uploadedImages;
}

$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$imageUrl = trim($_POST['image_url'] ?? '');
$uploadDir = __DIR__ . '/../../uploads/';
$defaultImage = 'http://localhost/Eshop/Backend/uploads/default.png';

if (trim($name) === '' || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Name and price are required']);
    exit;
}

$uploadedImages = [];

if (isset($_FILES['images'])) {
    $uploadedImages = uploadImages($_FILES['images'], $uploadDir);
} elseif (isset($_FILES['image'])) {
    $uploadedImages = uploadImages($_FILES['image'], $uploadDir);
}

$imagePath = $uploadedImages[0] ?? $defaultImage;
if (empty($uploadedImages) && !empty($imageUrl)) {
    $imagePath = $imageUrl;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmtCat->execute([$category_id]);
    if ($stmtCat->rowCount() === 0) {
        $category_id = null;
    }

    $stmt = $pdo->prepare("
        INSERT INTO products (name, description, price, stock, category_id, image, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$name, $description, $price, $stock, $category_id, $imagePath]);

    $productId = (int) $pdo->lastInsertId();

    if (count($uploadedImages) > 1) {
        $stmtImg = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
        foreach (array_slice($uploadedImages, 1) as $extraImage) {
            $stmtImg->execute([$productId, $extraImage]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully',
        'product_id' => $productId,
        'image' => $imagePath,
        'images' => $uploadedImages,
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}