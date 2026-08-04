<!DOCTYPE html>
<html>
<head>
    <title>Register Admin</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/admin_update.css">
</head>
<body>

<?php

if (isset($_POST['SUBMIT'])) {

    require_once __DIR__ . '/../config/mysqli_connect.php';

    $errors = array();

    if (empty($_POST['name'])) {
        $errors[] = "Please enter a name";
    } else {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    }

    if (empty($_POST['email'])) {
        $errors[] = "Please enter email.";
    } else {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    }

    if (empty($_POST['password'])) {
        $errors[] = "Please enter a password.";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if (empty($_POST['username'])) {
        $errors[] = "Please enter a username.";
    } else {
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    }

    if (empty($errors)) {
        $query = "INSERT INTO user(name, email, password, username) VALUES( '$name', '$email', '$password' , '$username')";
        $result = mysqli_query($conn, $query);

        if (mysqli_affected_rows($conn) == 1) {
            echo "<p class='message success'>Admin updated successfully!</p>";
        } else {
            echo "<p class='message error'>No matching admin found or no changes made.</p>";
        }
    } else {
        foreach ($errors as $msg) {
            echo "<p class='message error'>$msg</p>";
        }
    }

    mysqli_close($conn);
}

?>
<div class="update-container">
    <h2>Register Admin</h2>

    <form action="admin_registration.php" method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Name</label>
        <input type="text" name="name" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Username</label>
        <input type="text" name="username" required>

        <input type="submit" name="SUBMIT" value="SUBMIT">
        <button type="button" class="back-link" onclick="window.location.href='admin_dashboard.php'">Back to dashboard</button>
    </form>


</div>
</body>
</html>
