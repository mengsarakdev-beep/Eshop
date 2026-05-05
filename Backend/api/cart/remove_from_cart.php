<?php
// Enable full error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON response
header("Content-Type: application/json");
// Allow frontend origin (adjust if different)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");

// Start session
session_start();

// Get data from request
$data = json_decode(file_get_contents("php://input"), true);
$product_id = isset($data['product_id']) ? (string)$data['product_id'] : null;

// Initialize cart if not exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product_id exists in cart
if ($product_id && array_key_exists($product_id, $_SESSION['cart'])) {
    unset($_SESSION['cart'][$product_id]);
    $success = true;
    $message = "Product removed from cart.";
} else {
    $success = false;
    $message = "Product not found in cart.";
}

// Return updated cart and status
echo json_encode([
    "success" => $success,
    "message" => $message,
    "cart" => $_SESSION['cart'] // useful for frontend refresh
]);