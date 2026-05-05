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

require_once __DIR__ . '/../../api/src/config/db.php';

$input = json_decode(file_get_contents('php://input'), true);

$emailOrPhone = trim($input['emailOrPhone'] ?? '');
$password = $input['password'] ?? '';

if (!$emailOrPhone || !$password) {
    echo json_encode([
        'success' => false,
        'message' => 'Email or Phone and Password are required'
    ]);
    exit;
}

try {

    $pdo = Database::getInstance();

    $stmt = $pdo->prepare("
        SELECT id, username, email, phone, password, role, image
        FROM users
        WHERE email = ? OR phone = ?
        LIMIT 1
    ");

    $stmt->execute([$emailOrPhone, $emailOrPhone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Wrong password'
        ]);
        exit;
    }

    /* ----------- CREATE SESSION ----------- */

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['image'] = $user['image'] ?? 'default.png';

    session_regenerate_id(true);

    /* ----------- RESPONSE ----------- */

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'image' => $user['image'] ?? 'default.png'
        ]
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
?>