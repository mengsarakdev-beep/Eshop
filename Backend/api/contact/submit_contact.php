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

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$name = trim((string)($input['name'] ?? ''));
$email = trim((string)($input['email'] ?? ''));
$message = trim((string)($input['message'] ?? ''));
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

if ($name === '' || $email === '' || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in name, email, and message.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();
    ensureContactMessagesTable($pdo);

    $stmt = $pdo->prepare('
        INSERT INTO contact_messages (user_id, name, email, message, is_read, created_at)
        VALUES (?, ?, ?, ?, 0, NOW())
    ');
    $stmt->execute([$userId ?: null, $name, $email, $message]);

    echo json_encode([
        'success' => true,
        'message' => 'Your message has been sent successfully.',
        'contact_id' => (int)$pdo->lastInsertId(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error while sending your message.',
        'error' => $e->getMessage(),
    ]);
}
?>