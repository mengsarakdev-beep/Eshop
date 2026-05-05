<?php
// Admin-only create user endpoint
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_secure', 0);
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

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$password = $input['password'] ?? '';
$role = strtolower(trim($input['role'] ?? 'user'));

if (!$username || !$email || !$phone || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!in_array($role, ['user', 'admin'], true)) {
    $role = 'user';
}

require_once __DIR__ . '/../../api/src/config/db.php';

try {
    $pdo = Database::getInstance();

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR phone = ?');
    $stmt->execute([$email, $phone]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email or phone already registered']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$username, $email, $phone, $hashedPassword, $role]);

    echo json_encode(['success' => true, 'message' => 'User created', 'user_id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
