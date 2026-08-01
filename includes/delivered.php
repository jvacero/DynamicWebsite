<?php
session_start();
require_once __DIR__ . '/../config/mysqli_connect.php';

$orders = $_SESSION['orders'] ?? [];

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$pendingOrders = [];
$deliveredOrders = [];

foreach ($orders as $order_id => $order) {
    if (isset($order['status']) && $order['status'] === 'Pending') {
        if (isset($order['deliver_at']) && time() >= intval($order['deliver_at'])) {
            $orders[$order_id]['status'] = 'Delivered';
            $orders[$order_id]['delivered_at'] = date('Y-m-d H:i:s');
            $_SESSION['orders'][$order_id] = $orders[$order_id];
            $order = $orders[$order_id];
        }
    }

    if (isset($order['status']) && $order['status'] === 'Delivered') {
        $deliveredOrders[$order_id] = $order;
    } else {
        $pendingOrders[$order_id] = $order;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <style>
        .order-card { border: 1px solid #ddd; padding: 16px; margin-bottom: 16px; }
        .order-card h2 { margin-top: 0; }
        .order-items { margin: 0; padding-left: 20px; }
        .section-title { margin-top: 24px; }
    </style>
</head>
<body>
    <h1>Order History</h1>

    <?php if (!empty($pendingOrders)): ?>
        <h2 class="section-title">Pending Orders</h2>
        <?php foreach ($pendingOrders as $order): ?>
            <div class="order-card">
                <h3>Order <?php echo sanitize($order['order_id']); ?></h3>
                <p><strong>Status:</strong> <?php echo sanitize($order['status']); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?></p>
                <p><strong>Placed on:</strong> <?php echo sanitize($order['date']); ?></p>
                <?php if (isset($order['deliver_at'])): ?>
                    <?php $remaining = max(0, intval($order['deliver_at']) - time()); ?>
                    <p><strong>Time until delivery:</strong> <?php echo intval($remaining); ?> seconds</p>
                <?php endif; ?>
                <h4>Items</h4>
                <ul class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <li><?php echo sanitize($item['productname']); ?> x<?php echo (int)$item['quantity']; ?> - $<?php echo number_format($item['subtotal'], 2); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($deliveredOrders)): ?>
        <h2 class="section-title">Delivered Orders</h2>
        <?php foreach ($deliveredOrders as $order): ?>
            <div class="order-card">
                <h3>Order <?php echo sanitize($order['order_id']); ?></h3>
                <p><strong>Status:</strong> <?php echo sanitize($order['status']); ?></p>
                <p><strong>Total:</strong> $<?php echo number_format($order['total'], 2); ?></p>
                <p><strong>Placed on:</strong> <?php echo sanitize($order['date']); ?></p>
                <?php if (isset($order['delivered_at'])): ?>
                    <p><strong>Delivered on:</strong> <?php echo sanitize($order['delivered_at']); ?></p>
                <?php endif; ?>
                <h4>Items</h4>
                <ul class="order-items">
                    <?php foreach ($order['items'] as $item): ?>
                        <li><?php echo sanitize($item['productname']); ?> x<?php echo (int)$item['quantity']; ?> - $<?php echo number_format($item['subtotal'], 2); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (empty($pendingOrders) && empty($deliveredOrders)): ?>
        <p>No orders yet.</p>
    <?php endif; ?>

    <p><a href="../index.php">Back to Shop</a></p>
</body>
</html>
