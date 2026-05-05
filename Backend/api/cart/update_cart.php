<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

// Get input data
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'], $data['quantity'])) {
    echo json_encode([
        "success" => false,
        "message" => "Product ID and quantity required"
    ]);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity   = max(1, (int)$data['quantity']); // minimum 1

// Check if cart exists
if (!isset($_SESSION['cart'][$product_id])) {
    echo json_encode([
        "success" => false,
        "message" => "Product not in cart"
    ]);
    exit;
}

// Update quantity
$_SESSION['cart'][$product_id] = $quantity;

echo json_encode([
    "success" => true,
    "message" => "Quantity updated",
    "cart" => $_SESSION['cart']
]);
?>