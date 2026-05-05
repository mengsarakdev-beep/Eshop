<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type"); // ✅ important for preflight

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Preflight request, ច្រានចោល
    http_response_code(200);
    exit;
}

// Clear cart
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

echo json_encode([
    "success" => true,
    "message" => "Cart cleared"
]);