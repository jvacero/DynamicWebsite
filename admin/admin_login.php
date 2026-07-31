
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>

<?php
    include 'mysqli_connect.php';
    include 'config/login_proecess.php';
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
</div>


</body>
</html>

