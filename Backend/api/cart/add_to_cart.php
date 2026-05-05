<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['product_id'])) {
    echo json_encode(["success"=>false,"message"=>"Product ID required"]);
    exit;
}

$product_id = (int)$data['product_id'];
$quantity   = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if ($product_id <= 0 || $quantity < 1) {
    echo json_encode(["success"=>false,"message"=>"Invalid input"]);
    exit;
}

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "my_database";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die(json_encode(["success"=>false,"message"=>"DB fail"]));

// Check stock
$stmt = $conn->prepare("SELECT stock FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(["success"=>false,"message"=>"Product not found"]);
    exit;
}

$stock = (int)$row['stock'];
$currentQty = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] : 0;

if ($currentQty + $quantity > $stock) {
    echo json_encode([
        "success" => false,
        "message" => "Not enough stock. Available: $stock"
    ]);
    exit;
}

// Add to cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

echo json_encode([
    "success" => true,
    "message" => "Product added to cart",
    "cart" => $_SESSION['cart']
]);

$conn->close();