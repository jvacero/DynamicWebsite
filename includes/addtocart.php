<?php
session_start();
require_once __DIR__ . '/../config/mysqli_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (!isset($_SESSION['orders'])) {
    $_SESSION['orders'] = array();
}

$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;
$message = '';
$redirectToDelivered = false;

if ($user_id && empty($_SESSION['cart'])) {
    $cartStmt = $conn->prepare('SELECT product_id, quantity FROM cart WHERE user_id = ?');
    $cartStmt->bind_param('i', $user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    while ($row = $cartResult->fetch_assoc()) {
        $_SESSION['cart'][intval($row['product_id'])] = intval($row['quantity']);
    }
    $cartStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $product_id = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

        if ($product_id > 0 && $quantity > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }

            if ($user_id) {
                $cartSelect = $conn->prepare('SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?');
                $cartSelect->bind_param('ii', $user_id, $product_id);
                $cartSelect->execute();
                $cartResult = $cartSelect->get_result();

                if ($cartResult && $cartResult->num_rows === 1) {
                    $existing = $cartResult->fetch_assoc();
                    $newQuantity = intval($existing['quantity']) + $quantity;
                    $updateCart = $conn->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?');
                    $updateCart->bind_param('iii', $newQuantity, $user_id, $product_id);
                    $updateCart->execute();
                    $updateCart->close();
                } else {
                    $insertCart = $conn->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)');
                    $insertCart->bind_param('iii', $user_id, $product_id, $quantity);
                    $insertCart->execute();
                    $insertCart->close();
                }

                $cartSelect->close();
            }

            $message = 'Product added to cart!';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        $product_id = intval($_POST['product_id']);
        unset($_SESSION['cart'][$product_id]);

        if ($user_id) {
            $deleteCart = $conn->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?');
            $deleteCart->bind_param('ii', $user_id, $product_id);
            $deleteCart->execute();
            $deleteCart->close();
        }

        $message = 'Product removed from cart!';
    }

    if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
        if (!empty($_SESSION['cart'])) {
            $cartItemsForOrder = array();
            $order_total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
            $customer_name = isset($_SESSION['name']) ? sanitize($_SESSION['name']) : '';
            $customer_email = isset($_SESSION['email']) ? sanitize($_SESSION['email']) : '';
            $deliverSeconds = rand(30, 60);
            $insufficientStock = false;

            $conn->begin_transaction();

            $checkStmt = $conn->prepare('SELECT productname, price, stock FROM product WHERE id = ?');
            $updateStmt = $conn->prepare('UPDATE product SET stock = stock - ? WHERE id = ? AND stock >= ?');

            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $product_id = intval($product_id);
                $quantity = max(1, intval($quantity));

                $checkStmt->bind_param('i', $product_id);
                $checkStmt->execute();
                $result = $checkStmt->get_result();

                if (!$result || $result->num_rows !== 1) {
                    $insufficientStock = true;
                    break;
                }

                $product = $result->fetch_assoc();
                if (intval($product['stock']) < $quantity) {
                    $insufficientStock = true;
                    break;
                }

                $updateStmt->bind_param('iii', $quantity, $product_id, $quantity);
                $updateStmt->execute();
                if ($updateStmt->affected_rows !== 1) {
                    $insufficientStock = true;
                    break;
                }

                $cartItemsForOrder[] = array(
                    'id' => $product_id,
                    'productname' => $product['productname'],
                    'price' => (float)$product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $quantity * (float)$product['price']
                );
            }

            if ($insufficientStock) {
                $conn->rollback();
                $message = 'Checkout failed: insufficient stock for one or more items.';
            } else {
                $conn->commit();
                $order_id = uniqid('ORD_');

                $_SESSION['order'] = array(
                    'order_id' => $order_id,
                    'customer_name' => $customer_name,
                    'customer_email' => $customer_email,
                    'shipping_address' => '',
                    'items' => $cartItemsForOrder,
                    'total' => $order_total,
                    'status' => 'Pending',
                    'date' => date('Y-m-d H:i:s'),
                    'deliver_at' => time() + $deliverSeconds,
                    'deliver_delay' => $deliverSeconds
                );
                $_SESSION['orders'][$order_id] = $_SESSION['order'];

                if ($user_id) {
                    $clearCart = $conn->prepare('DELETE FROM cart WHERE user_id = ?');
                    $clearCart->bind_param('i', $user_id);
                    $clearCart->execute();
                    $clearCart->close();
                }

                $_SESSION['cart'] = array();
                $message = 'Order placed successfully!';
                $redirectToDelivered = true;
            }

            $checkStmt->close();
            $updateStmt->close();
        } else {
            $message = 'Cart is empty!';
        }
    }
}

if ($redirectToDelivered) {
    header('Location: delivered.php');
    exit;
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

$cartItems = array();
$cartTotal = 0.0;
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    if (!empty($ids)) {
        $sql = 'SELECT * FROM product WHERE id IN (' . implode(',', $ids) . ')';
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $qty = $_SESSION['cart'][$row['id']] ?? 0;
                $subtotal = $qty * $row['price'];
                $row['quantity'] = $qty;
                $row['subtotal'] = $subtotal;
                $cartTotal += $subtotal;
                $cartItems[$row['id']] = $row;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        .message { margin: 16px 0; padding: 12px; background: #e8f5e9; border: 1px solid #c8e6c9; }
        .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .cart-table th, .cart-table td { border: 1px solid #ddd; padding: 8px; }
        .cart-table th { background: #f4f4f4; }
        .actions { margin-top: 16px; }
    </style>
</head>
<body>
    <h1>Your Cart</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($cartItems)): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['productname']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo (int)$item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td>
                            <form method="POST" action="addtocart.php">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo (int)$item['id']; ?>">
                                <button type="submit">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Total:</strong> $<?php echo number_format($cartTotal, 2); ?></p>

        <div class="actions">
            <form method="POST" action="addtocart.php">
                <input type="hidden" name="action" value="checkout">
                <input type="hidden" name="total" value="<?php echo htmlspecialchars($cartTotal); ?>">
                <button type="submit">Checkout</button>
            </form>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <p><a href="../index.php">Continue Shopping</a></p>
    <p><a href="delivered.php">View Order History</a></p>
</body>
</html>

