<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}
include('db.php');
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}
$orderNumber = 'AFC-' . strtoupper(substr(uniqid(), -6));
$userId = $_SESSION['user_id'];
$orderType = $data['order_type'];
$paymentMethod = $data['payment_method'];
$total = $data['total'];
$items = json_encode($data['items']);
$stmt = $conn->prepare("INSERT INTO orders (order_number, user_id, order_type, payment_method, total, items) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissds", $orderNumber, $userId, $orderType, $paymentMethod, $total, $items);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
$stmt->close();
$conn->close();
?>