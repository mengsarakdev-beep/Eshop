<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?: [];

$user_id = intval($input['user_id'] ?? 0);
$name = trim((string)($input['name'] ?? ''));
$phone = trim((string)($input['phone'] ?? ''));
$address = trim((string)($input['address'] ?? ''));
$cart_items = is_array($input['cart_items'] ?? null) ? $input['cart_items'] : [];
$payment_method = strtoupper(trim((string)($input['payment_method'] ?? 'ABA')));

if ($user_id <= 0 || $name === '' || $phone === '' || $address === '' || empty($cart_items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please provide name, phone, address, and cart items.'
    ]);
    exit;
}

try {
    $pdo = Database::getInstance();
    $pdo->beginTransaction();

    $total = 0.0;
    $products = [];

    foreach ($cart_items as $item) {
        $product_id = intval($item['product_id'] ?? 0);
        $quantity = intval($item['quantity'] ?? 0);

        if ($product_id <= 0 || $quantity <= 0) {
            throw new Exception('Invalid product or quantity.');
        }

        $stmt = $pdo->prepare("SELECT id, name, price, stock FROM products WHERE id = ? LIMIT 1");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found: {$product_id}");
        }

        if ((int)$product['stock'] < $quantity) {
            throw new Exception("Insufficient stock for {$product['name']}");
        }

        $unit_price = isset($item['price']) ? (float)$item['price'] : (float)$product['price'];
        $subtotal = $unit_price * $quantity;
        $total += $subtotal;

        $products[] = [
            'id' => $product_id,
            'price' => $unit_price,
            'quantity' => $quantity,
        ];
    }

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, address, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $total, $address]);
    $order_id = (int)$pdo->lastInsertId();

    foreach ($products as $product) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product['id'], $product['quantity'], $product['price']]);

        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$product['quantity'], $product['id']]);
    }

    $pdo->commit();

    $transaction_id = 'ABA-' . strtoupper(substr(sha1($order_id . '|' . microtime(true)), 0, 12));
    $qr_url = 'http://localhost/Eshop/public/aba-qr.png';

    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'order_id' => $order_id,
        'total' => $total,
        'amount_khr' => round($total * 4100),
        'payment_method' => $payment_method,
        'qr_url' => $qr_url,
        'transaction_id' => $transaction_id,
        'status' => 'pending',
        'customer' => [
            'name' => $name,
            'phone' => $phone,
            'address' => $address,
        ],
    ]);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>