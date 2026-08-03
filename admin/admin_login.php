
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>

<?php
    require_once __DIR__ . '/../config/login_process.php';
?>

<div>
    <span>
        Admin Login
    </span><br><br>
    <form method="POST">
        <input placeholder="email" type="text" name="email"><br><br>
        <input placeholder="password" type="password" name="password"><br><br>
        <button>Log in</button>
    </form>
    <a href="admin_registration.php"><button action="">Register</button></a>
</div>


</body>
</html>

