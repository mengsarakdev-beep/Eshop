<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY id DESC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'categories' => $categories]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>