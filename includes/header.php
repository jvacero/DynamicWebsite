<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userName = $_SESSION['name'] ?? null;
?>
<header class="fixed-header">
    <div class="header-brand">
        <a class="logo-link" href="./index.php">
            <img class="logo-icon" src="uploads/1785600522_Pixel-Art-Watermelon-6.webp" alt="Pixel Logo">
            <span>Buraot</span>
        </a>
    </div>
    <nav class="site-nav">
        <?php if ($userName): ?>
            <span class="greeting">Welcome, <?php echo htmlspecialchars($userName); ?></span>
            <div class="actions">
                <a class="button cart" href="includes/addtocart.php">Cart</a>
                <a class="button logout" href="config/logout.php">Logout</a>
            </div>
        <?php else: ?>
            <a class="button login" href="admin/admin_login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
