<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../src/config/db.php';

function ensureContactMessagesTable(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) DEFAULT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id),
            KEY idx_contact_messages_created_at (created_at),
            KEY idx_contact_messages_is_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$id = intval($input['id'] ?? 0);

try {
    $pdo = Database::getInstance();
    ensureContactMessagesTable($pdo);

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
        $stmt->execute([$id]);
    } else {
        $pdo->exec('UPDATE contact_messages SET is_read = 1 WHERE is_read = 0');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Contact notifications updated successfully.'
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to update contact notifications.',
        'error' => $e->getMessage(),
    ]);
}
?>