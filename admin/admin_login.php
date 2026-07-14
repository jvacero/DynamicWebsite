<?php
    include("admin_header.php")
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>

<div>
    <span>
        Admin Login
    </span>
    <form method="POST">
        <input placeholder="email" type="text" name="email">
        <input placeholder="password" type="password" name="password">
        <button>Log in</button>
    </form>
    <a href="../admin/dashboard.php">go to dashboard</a>
    <a href="../admin/dbquery_localdb.php">no database yet? click to add database locally</a>
</div>
<div>
    <span>
        Call administrator to create an admin account for new staff.
    </span>
</div>


</body>
</html>

<?php
    include("admin_footer.php")
?>