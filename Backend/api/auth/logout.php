<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session
session_start();

/* ----------- DESTROY SESSION ----------- */

// Clear session array
$_SESSION = [];

// Destroy session
session_unset();
session_destroy();

/* ----------- REMOVE SESSION COOKIE ----------- */

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* ----------- OPTIONAL TOKEN COOKIE ----------- */

if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 3600, '/', 'localhost', false, true);
}

/* ----------- RESPONSE ----------- */

echo json_encode([
    "success" => true,
    "message" => "Logout successful"
]);

exit();