<!DOCTYPE html>
<html>
<head>
    <title>Update Admin</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

if (isset($_POST['UPDATE'])) {

    require('../config/mysqli_connect.php');

    $errors = array();

    // Old email (used to find the record)
    if (empty($_POST['old_email'])) {
        $errors[] = "Please enter the current email.";
    } else {
        $old_email = mysqli_real_escape_string($conn, trim($_POST['old_email']));
    }

    // New name
    if (empty($_POST['name'])) {
        $errors[] = "Please enter a name";
     
    } else {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    }

    // New password
    if (empty($_POST['password'])) {
        $errors[] = "Please enter a password.";
    } else {
        $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    }

    if (empty($errors)) {

        $query = "UPDATE admin
                  SET name='$name',
                      password='$password'
                  WHERE email='$old_email'";

        $result = mysqli_query($conn, $query);

        if (mysqli_affected_rows($conn) == 1) {
            echo "<p style='color:green;'>Admin updated successfully!</p>";
        } else {
            echo "<p style='color:red;'>No matching admin found or no changes made.</p>";
        }

    } else {

        foreach ($errors as $msg) {
            echo "<p style='color:red;'>$msg</p>";
        }

    }

    mysqli_close($conn);
}

?>

<form action="admin_update.php" method="POST">

    <label>Current Email</label><br>
    <input type="email" name="old_email" required><br><br>

    <label>New Name</label><br>
    <input type="text" name="name" required ><br><br>

    <label>New Password</label><br>
    <input type="text" name="password" required><br><br>

    <input type="submit" name="UPDATE" value="UPDATE">

</form>

</body>
</html>