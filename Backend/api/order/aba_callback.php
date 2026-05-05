<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?: [];
$transaction_id = $data['transaction_id'] ?? null;
$status = $data['status'] ?? 'paid';

if (!$transaction_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Transaction ID is required'
    ]);
    exit;
}

http_response_code(200);
echo json_encode([
    'success' => true,
    'transaction_id' => $transaction_id,
    'status' => $status,
    'message' => 'Callback received successfully'
]);
?>