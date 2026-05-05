<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/../../api/src/config/db.php';

$user_id = intval($_GET['user_id'] ?? 0);
$all = isset($_GET['all']) && $_GET['all'] == '1';

if (!$all && !$user_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    $pdo = Database::getInstance();

    $sql = "
        SELECT
            o.id AS order_id,
            o.total_amount,
            o.address,
            o.created_at AS order_date,
            u.username AS username,
            u.email,
            u.phone,
            oi.product_id,
            oi.quantity,
            oi.price AS item_price,
            pr.name AS product_name,
            pr.image AS product_image
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products pr ON oi.product_id = pr.id
    ";

    $params = [];
    if (!$all) {
        $sql .= " WHERE o.user_id = ?";
        $params[] = $user_id;
    }

    $sql .= " ORDER BY o.created_at DESC, oi.id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo json_encode(['success' => true, 'orders' => []]);
        exit;
    }

    $orders = [];
    foreach ($rows as $row) {
        $order_id = (int)$row['order_id'];
        $hasItems = !empty($row['product_id']);
        $derivedTransactionId = 'ABA-ORDER-' . str_pad((string)$order_id, 6, '0', STR_PAD_LEFT);

        if (!isset($orders[$order_id])) {
            $orders[$order_id] = [
                'order_id' => $order_id,
                'total_amount' => (float)$row['total_amount'],
                'created_at' => $row['order_date'],
                'order_date' => $row['order_date'],
                'address' => $row['address'] ?: '',
                'username' => $row['username'] ?: 'Customer',
                'email' => $row['email'] ?: '',
                'phone' => $row['phone'] ?: '',
                'payment_method' => 'ABA',
                'transaction_id' => $derivedTransactionId,
                'items' => []
            ];
        }

        if ($hasItems) {
            $orders[$order_id]['items'][] = [
                'product_id' => (int)$row['product_id'],
                'product_name' => $row['product_name'] ?: 'Product',
                'name' => $row['product_name'] ?: 'Product',
                'product_image' => $row['product_image'] ?: '',
                'price' => (float)$row['item_price'],
                'quantity' => (int)$row['quantity']
            ];
        }
    }

    echo json_encode(['success' => true, 'orders' => array_values($orders)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>