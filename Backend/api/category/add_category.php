<?php
// add_category.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request for OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Correct path to db.php (adjust to your folder structure)
$path = __DIR__ . '/../../api/src/config/db.php';
if (!file_exists($path)) {
    echo json_encode(['success' => false, 'message' => "db.php NOT FOUND at: $path"]);
    exit;
}

require_once $path; // make sure db.php has Database::getInstance() or $conn PDO

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['name']) || trim($input['name']) === '') {
    echo json_encode(['success' => false, 'message' => 'Category name is required']);
    exit;
}

$name = trim($input['name']);

try {
    // Use PDO instance from db.php
    $pdo = Database::getInstance(); // or $conn if using $conn from db.php

    // Check if category already exists
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Category already exists']);
        exit;
    }

    // Insert new category
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);

    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully',
        'category_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>