<?php
setcookie(
    "remember_me",
    "John",
    time() + (86400 * 30), // 30 days
    "/"
);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies</title>
</head>
<body>
    <?php
if (isset($_COOKIE['remember_me'])) {
    echo $_COOKIE['remember_me'];
}
echo "$_COOKIE";
    ?>
</body>
</html>