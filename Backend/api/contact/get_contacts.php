<?php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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

$limit = intval($_GET['limit'] ?? 20);
$limit = max(1, min($limit, 100));

try {
    $pdo = Database::getInstance();
    ensureContactMessagesTable($pdo);

    $countRow = $pdo->query('
        SELECT
            COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END), 0) AS unread
        FROM contact_messages
    ')->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'unread' => 0];

    $stmt = $pdo->query("SELECT id, user_id, name, email, message, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT {$limit}");
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'total' => (int)($countRow['total'] ?? 0),
        'unread' => (int)($countRow['unread'] ?? 0),
        'contacts' => $contacts,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to load contact messages.',
        'error' => $e->getMessage(),
    ]);
}
?>