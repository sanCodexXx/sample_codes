<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('db.php');
$orderId = $_GET['order_id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
if (!$order) {
    die("Order not found.");
}
$items = json_decode($order['items'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #<?= htmlspecialchars($order['order_number']) ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 400px;
            margin: auto;
            padding: 20px;
            color: #333;
        }
        h2, p { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border-bottom: 1px dashed #ccc; padding: 8px; text-align: left; }
        .total { font-weight: bold; font-size: 1.2rem; }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <h2>Authentic Filipino Cuisine</h2>
    <p>Order #<?= htmlspecialchars($order['order_number']) ?></p>
    <p><?= $order['order_type'] === 'dine-in' ? 'Dine In' : 'Take Out' ?> | <?= htmlspecialchars($order['payment_method']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>₱<?= number_format($item['price'] * $item['qty'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">Total: ₱<?= number_format($order['total'], 2) ?></p>
    <p style="margin-top:20px; text-align:center;">Thank you for your order!</p>
    <div style="text-align:center;">
        <button onclick="window.print()" style="padding:0.5rem 1.5rem; font-size:1rem; background:#A67B5B; color:white; border:none; border-radius:0.5rem; cursor:pointer;">Print / Download</button>
    </div>
</body>
</html>