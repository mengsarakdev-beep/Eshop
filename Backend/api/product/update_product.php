<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

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

$id = intval($_POST['id'] ?? 0);
$name = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$price = floatval($_POST['price'] ?? 0);
$stock = intval($_POST['stock'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);
$imageUrl = trim($_POST['image_url'] ?? '');
$uploadDir = __DIR__ . '/../../uploads/';

if (!$id || trim($name) === '' || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    $stmtCat = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
    $stmtCat->execute([$category_id]);
    if ($stmtCat->rowCount() === 0) {
        $category_id = null;
    }

    $currentImage = $product['image'] ?? 'http://localhost/Eshop/Backend/uploads/default.png';
    $uploadedImages = [];

    if (isset($_FILES['images'])) {
        $uploadedImages = uploadImages($_FILES['images'], $uploadDir);
    } elseif (isset($_FILES['image'])) {
        $uploadedImages = uploadImages($_FILES['image'], $uploadDir);
    }

    if (!empty($uploadedImages)) {
        $stmtExtra = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
        $stmtExtra->execute([$id]);
        $existingExtraImages = $stmtExtra->fetchAll(PDO::FETCH_COLUMN);

        foreach ($existingExtraImages as $existingExtraImage) {
            deleteImageFile($existingExtraImage);
        }

        $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$id]);
        deleteImageFile($currentImage);

        $currentImage = $uploadedImages[0];

        if (count($uploadedImages) > 1) {
            $stmtInsertExtra = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
            foreach (array_slice($uploadedImages, 1) as $extraImage) {
                $stmtInsertExtra->execute([$id, $extraImage]);
            }
        }
    } elseif (!empty($imageUrl)) {
        $currentImage = $imageUrl;
    }

    $stmt = $pdo->prepare("
        UPDATE products
        SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
        WHERE id = ?
    ");
    $stmt->execute([$name, $description, $price, $stock, $category_id, $currentImage, $id]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Product updated successfully',
        'image' => $currentImage,
        'images' => $uploadedImages,
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>