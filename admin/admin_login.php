<?php
require_once __DIR__ . '/../config/login_process.php';

$loginError = $loginError ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/admin_login.css" type="text/css">
</head>
<body>
    <main class="login-page">
        <section class="login-container">
            <h1>Login</h1>

            <?php if (!empty($loginError)): ?>
                <div class="message error"><?php echo htmlspecialchars($loginError); ?></div>
            <?php endif; ?>

            <form class="login-form" method="POST" action="admin_login.php">
                <label for="email">Email</label>
                <input id="email" placeholder="Email" type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>

                <label for="password">Password</label>
                <input id="password" placeholder="Password" type="password" name="password" required>

                <button type="submit">Log In</button>
            </form>

            <a class="register-link" href="admin_registration.php">Register</a>
        </section>
    </main>
</body>
</html>

