<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");

session_start();

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "my_database";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die(json_encode(["success"=>false,"message"=>"DB fail"]));

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo json_encode(["success"=>true, "items"=>[]]);
    exit;
}

$items = [];
$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id=?");

foreach ($_SESSION['cart'] as $product_id => $qty) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $items[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "price" => $row['price'],
            "qty" => $qty
        ];
    }
}

$stmt->close();
$conn->close();

echo json_encode([
    "success" => true,
    "items" => $items
]);