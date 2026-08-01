<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$userName = $_SESSION['name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/user_style.css/" type="text/css" media="screen">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="fixed-header">
        <div class="container">
            <nav>
                <?php if ($userName): ?>
                    <span>Welcome, <?php echo htmlspecialchars($userName); ?></span>
                    <a href="includes/addtocart.php">Cart</a>
                    <a href="config/logout.php">Logout</a>
                <?php else: ?>
                    <a href="admin/admin_login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</body>

</html>