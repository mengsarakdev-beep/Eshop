<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../../api/src/config/db.php';

// Read POST JSON data
$input = json_decode(file_get_contents('php://input'), true);

$username = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phoneNumber'] ?? '');
$password = $input['password'] ?? '';

if (!$username || !$email || !$phone || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

try {
    $pdo = Database::getInstance();

    // Check if email or phone already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or phone already registered.']);
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user, default role user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
    $stmt->execute([$username, $email, $phone, $hashedPassword]);

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful!',
        'user_id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>