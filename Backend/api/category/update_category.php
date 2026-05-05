<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../../api/src/config/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($input['id'] ?? 0);
$name = trim($input['name'] ?? '');

if (!$id || !$name) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
    echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>