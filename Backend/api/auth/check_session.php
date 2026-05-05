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

/* ----------- CHECK SESSION ----------- */

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {

    echo json_encode([
        'loggedIn' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'phone' => $_SESSION['phone'] ?? '',
            'role' => $_SESSION['role'] ?? 'user',
            'image' => $_SESSION['image'] ?? 'default.png'
        ]
    ]);

} else {

    echo json_encode([
        'loggedIn' => false
    ]);
}
?>