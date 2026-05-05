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
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid category id']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>