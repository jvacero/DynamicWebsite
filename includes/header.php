<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userName = $_SESSION['name'] ?? null;
?>
<header class="fixed-header">
    <div class="header-brand">
        <a class="logo-link" href="./index.php">
            <img class="logo-icon" src="uploads\ChatGPT Image Aug 5, 2026, 12_33_18 AM.png" alt="Pixel Logo">
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
            <!-- Example Trigger Button -->
<button type="button" class="button action-button" onclick="openAdminLoginModal()">
    Login
</button>
            <a class="button login" href="admin/admin_login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
