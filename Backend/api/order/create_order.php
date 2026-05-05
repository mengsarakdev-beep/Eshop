<?php
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

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$user_id = intval($input['user_id'] ?? 0);
$cart_items = is_array($input['cart_items'] ?? null) ? $input['cart_items'] : [];
$payment_method = strtoupper(trim((string)($input['payment_method'] ?? 'ABA')));
$total_amount = floatval($input['total_amount'] ?? 0);
$address = trim((string)($input['address'] ?? ''));

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

if (empty($cart_items) || $address === '') {
    echo json_encode(['success' => false, 'message' => 'Cart is empty or address is required']);
    exit;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $calculated_total = 0.0;

    foreach ($cart_items as $item) {
        $product_id = intval($item['product_id'] ?? 0);
        $quantity = intval($item['quantity'] ?? 0);

        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception('Invalid product or quantity');
        }

        $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found (ID: {$product_id})");
        }

        if ((int)$product['stock'] < $quantity) {
            throw new Exception("Insufficient stock for {$product['name']}");
        }

        $unit_price = isset($item['price']) ? floatval($item['price']) : (float)$product['price'];
        $calculated_total += $unit_price * $quantity;
    }

    if ($total_amount <= 0) {
        $total_amount = $calculated_total;
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $total_amount, $address]);
    $order_id = (int)$pdo->lastInsertId();

    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $order_id,
            intval($item['product_id'] ?? 0),
            intval($item['quantity'] ?? 0),
            floatval($item['price'] ?? 0),
        ]);

        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([
            intval($item['quantity'] ?? 0),
            intval($item['product_id'] ?? 0),
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $order_id,
        'total' => $total_amount,
        'payment_method' => $payment_method,
        'address' => $address,
        'status' => 'completed'
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Checkout failed: ' . $e->getMessage()
    ]);
}
?>