<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/pixel-icon-library/fonts/iconfont.css">
    <link rel="stylesheet" href="./assets/dashboard.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body class="card-body">
    <hr>
    <a href="admin/dashboard.php" class="button"></a>
    <form action="admin/admin_registration.php"><button type="submit">Admin Registration</button></form>
    <form action="admin/admin_update.php"><button type="submit">Admin Update</button></form>
    <form action="admin/admin_dashboard.php"><button type="submit">Admin Dashboard</button></form>
    <form action="config/dbquery_localdb.php"><button type="submit">Local Database</button></form>
    <hr>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../config/mysqli_connect.php';

    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
    ?>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="card-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo htmlspecialchars($row['productimage']); ?>" alt="<?php echo htmlspecialchars($row['productname']); ?>">
                    <h3><?php echo htmlspecialchars($row['productname']); ?></h3>
                    <p> $<?php echo number_format($row['price'], 2); ?></p>
                    <p> Stock: <?php echo (int)$row['stock']; ?></p>

                    <?php if ($row['stock'] > 0): ?>
                        <?php if (isset($_SESSION['id'])): ?>
                            <form action="includes/addtocart.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button class="butsub" type="submit">Add To Cart</button>
                            </form>
                        <?php else: ?>
                            <a href="admin/admin_login.php"><button type="button">Login to Buy</button></a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button type="button" disabled>Out of Stock</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No items available right now.</p>
    <?php endif; ?>


    





</body>
</html>