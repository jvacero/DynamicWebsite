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
        body {
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            background: #ffe3ae;
            color: #000000;
            min-height: 100vh;
            padding: 120px 1rem 120px;
        }

        .order-page {
            max-width: 1080px;
            margin: 0 auto;
            background: #ffffff;
            border: 5px solid #000000;
            box-shadow: 0 6px 0 #000000;
            padding: 1.5rem;
        }

        h1 {
            margin: 0 0 1rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .order-card {
            border: 5px solid #000000;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fff3cd;
            box-shadow: 0 6px 0 #000000;
        }

        .order-card h2 {
            margin-top: 0;
        }

        .order-items {
            margin: 0;
            padding-left: 1.25rem;
        }

        .section-title {
            margin-top: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        a {
            color: #000000;
            text-decoration: none;
            font-weight: 700;
        }

        a:hover,
        a:focus {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <main class="order-page">
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
    </main>
</body>
</html>
