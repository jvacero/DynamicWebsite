<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/mysqli_connect.php';

$sql = "SELECT * FROM product";
$result = $conn->query($sql);
?>
<section class="product-dashboard">
    <div class="dashboard-header">
        <div>
            <h1>Buraot Online Store</h1>
            <p>Discover low prices products and add them to your cart.</p>
        </div>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <div class="card-grid">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <div class="card-image-wrap">
                        <img class="product-image" src="uploads/<?php echo htmlspecialchars($row['productimage']); ?>" alt="<?php echo htmlspecialchars($row['productname']); ?>">
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($row['productname']); ?></h3>
                        <p class="price">$<?php echo number_format($row['price'], 2); ?></p>
                        <p class="stock">Stock: <?php echo (int)$row['stock']; ?></p>
                        <div class="card-actions">
                            <?php if ($row['stock'] > 0): ?>
                                <?php if (isset($_SESSION['id'])): ?>
                                    <form action="includes/addtocart.php" method="POST">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button class="butsub" type="submit">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <a class="button action-button" href="admin/admin_login.php">Login to Buy</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="empty-state">No items available right now.</p>
    <?php endif; ?>

    <div class="dashboard-links" style="visibility:hidden">
        <a class="small-button" href="admin/admin_dashboard.php">Admin Dashboard</a>
        <a class="small-button" href="admin/admin_registration.php">Admin Registration</a>
        <a class="small-button" href="admin/admin_update.php">Admin Update</a>
        <a class="small-button" href="config/dbquery_localdb.php">Local Database</a>
    </div>
</section>
