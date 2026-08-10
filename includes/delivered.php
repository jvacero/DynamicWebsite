<?php
session_start();
require_once __DIR__ . '/../config/mysqli_connect.php';

// Check for both common session key names for user ID
$userId = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

function sanitize($input) {
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

$orders = $_SESSION['orders'] ?? [];

// Process order expiration and sync to database
foreach ($orders as $order_id => $order) {
    if (isset($order['status']) && $order['status'] === 'Pending') {
        if (isset($order['deliver_at']) && time() >= intval($order['deliver_at'])) {
            
            $deliveredTime = date('Y-m-d H:i:s');
            $orders[$order_id]['status'] = 'Delivered';
            $orders[$order_id]['delivered_at'] = $deliveredTime;

            // Only persist to DB if user is authenticated and order hasn't been saved yet
            if ($userId && empty($order['db_persisted'])) {
                $conn->begin_transaction();
                try {
                    $orderRef = $order['order_id'] ?? ('ORD-' . $order_id);
                    $total = floatval($order['total'] ?? 0.00);
                    $shippingAddress = $order['shipping_address'] ?? null;
                    $status = 'Delivered';

                    // 1. Insert into order_history
                    $stmt = $conn->prepare("INSERT INTO order_history (user_id, order_reference, total, status, shipping_address, delivered_at) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isdsss", $userId, $orderRef, $total, $status, $shippingAddress, $deliveredTime);
                    $stmt->execute();
                    $dbOrderId = $stmt->insert_id;
                    $stmt->close();

                    // 2. Insert into order_items
                    if (!empty($order['items']) && is_array($order['items'])) {
                        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
                        foreach ($order['items'] as $item) {
                            $productId = intval($item['product_id'] ?? $item['id']);
                            $quantity = intval($item['quantity']);
                            $price = floatval($item['price']);
                            $subtotal = floatval($item['subtotal'] ?? ($price * $quantity));

                            $itemStmt->bind_param("iiidd", $dbOrderId, $productId, $quantity, $price, $subtotal);
                            $itemStmt->execute();
                        }
                        $itemStmt->close();
                    }

                    $conn->commit();
                    $orders[$order_id]['db_persisted'] = true;
                } catch (Exception $e) {
                    $conn->rollback();
                    error_log("Failed to insert order into DB: " . $e->getMessage());
                }
            }

            $_SESSION['orders'][$order_id] = $orders[$order_id];
        }
    }
}

// Fetch database order history for the logged-in user
$dbOrders = [];
if ($userId) {
    $historySql = "
        SELECT 
            oh.id AS db_order_id,
            oh.order_reference,
            oh.total,
            oh.status,
            oh.created_at,
            oh.delivered_at,
            oi.quantity,
            oi.price,
            oi.subtotal,
            p.productname
        FROM order_history oh
        JOIN order_items oi ON oi.order_id = oh.id
        JOIN product p ON p.id = oi.product_id
        WHERE oh.user_id = ?
        ORDER BY oh.created_at DESC
    ";
    
    if ($stmt = $conn->prepare($historySql)) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $ref = $row['order_reference'];
            if (!isset($dbOrders[$ref])) {
                $dbOrders[$ref] = [
                    'order_id' => $ref,
                    'status' => $row['status'],
                    'total' => $row['total'],
                    'date' => $row['created_at'],
                    'delivered_at' => $row['delivered_at'],
                    'items' => []
                ];
            }
            $dbOrders[$ref]['items'][] = [
                'productname' => $row['productname'],
                'quantity' => $row['quantity'],
                'subtotal' => $row['subtotal']
            ];
        }
        $stmt->close();
    }
}

// Separate pending vs delivered session orders
$pendingOrders = [];
$deliveredOrders = [];

foreach ($orders as $order_id => $order) {
    if (isset($order['status']) && $order['status'] === 'Delivered') {
        $deliveredOrders[$order_id] = $order;
    } else {
        $pendingOrders[$order_id] = $order;
    }
}

// Merge persistent DB delivered orders into $deliveredOrders
foreach ($dbOrders as $ref => $dbOrder) {
    if (!isset($deliveredOrders[$ref])) {
        $deliveredOrders[$ref] = $dbOrder;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Pixel Arcade</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Press Start 2P', 'Courier New', monospace;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
        }

        body {
            margin: 0;
            background-color: #2a2a3e;
            background-image: 
                linear-gradient(45deg, #1e1e2c 25%, transparent 25%), 
                linear-gradient(-45deg, #1e1e2c 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #1e1e2c 75%), 
                linear-gradient(-45deg, transparent 75%, #1e1e2c 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            color: #000000;
            min-height: 100vh;
            padding: 40px 1rem 80px;
        }

        .order-page {
            max-width: 1080px;
            margin: 0 auto;
            background: #ffffff;
            border: 5px solid #000000;
            box-shadow: 8px 8px 0 #000000;
            padding: 2rem;
        }

        h1 {
            margin: 0 0 1.5rem;
            text-transform: uppercase;
            font-size: 1.2rem;
            line-height: 1.4;
            color: #000000;
            background: #ffcc00;
            display: inline-block;
            padding: 10px 15px;
            border: 4px solid #000000;
            box-shadow: 4px 4px 0 #000000;
        }

        .section-title {
            margin-top: 2rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            font-size: 0.9rem;
            color: #ffffff;
            background: #ff7b00;
            padding: 8px 12px;
            border: 3px solid #000000;
            box-shadow: 3px 3px 0 #000000;
            display: inline-block;
        }

        .order-card {
            border: 4px solid #000000;
            padding: 1.2rem;
            margin-bottom: 1.2rem;
            background: #fff3cd;
            box-shadow: 5px 5px 0 #000000;
        }

        .order-card h3 {
            margin-top: 0;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #000000;
            border-bottom: 2px dashed #000000;
            padding-bottom: 8px;
        }

        .order-card p {
            font-size: 0.65rem;
            line-height: 1.6;
            margin: 8px 0;
        }

        .order-card h4 {
            font-size: 0.7rem;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .order-items {
            margin: 0;
            padding-left: 1.2rem;
            font-size: 0.6rem;
            line-height: 1.8;
        }

        .order-items li {
            margin-bottom: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border: 2px solid #000000;
            font-size: 0.6rem;
            font-weight: bold;
        }

        .status-pending {
            background: #00b0ff;
            color: #000000;
        }

        .status-delivered {
            background: #00e676;
            color: #000000;
        }

        .btn-back {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 10px 16px;
            background: #00b0ff;
            color: #000000;
            text-decoration: none;
            border: 3px solid #000000;
            box-shadow: 4px 4px 0 #000000;
            font-size: 0.7rem;
            text-transform: uppercase;
            cursor: pointer;
        }

        .btn-back:hover {
            background: #00e676;
        }

        .btn-back:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #000000;
        }
    </style>
</head>
<body>
    <main class="order-page">
        <h1>Order History</h1>

        <?php if (!empty($pendingOrders)): ?>
            <div><h2 class="section-title">Pending Orders</h2></div>
            <?php foreach ($pendingOrders as $order): ?>
                <div class="order-card">
                    <h3>Order <?php echo sanitize($order['order_id']); ?></h3>
                    <p><strong>Status:</strong> <span class="status-badge status-pending"><?php echo sanitize($order['status']); ?></span></p>
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
            <div><h2 class="section-title">Delivered Orders</h2></div>
            <?php foreach ($deliveredOrders as $order): ?>
                <div class="order-card">
                    <h3>Order <?php echo sanitize($order['order_id']); ?></h3>
                    <p><strong>Status:</strong> <span class="status-badge status-delivered"><?php echo sanitize($order['status']); ?></span></p>
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
            <p style="font-size: 0.7rem; margin-top: 1rem;">No orders found.</p>
        <?php endif; ?>

        <p><a href="../index.php" class="btn-back">Back to Shop</a></p>
    </main>
</body>
</html>