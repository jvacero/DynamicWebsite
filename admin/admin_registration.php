<!DOCTYPE html>
<html>
<head>
    <title>Update Admin</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

if (isset($_POST['SUBMIT'])) {

    require('../config/mysqli_connect.php');

    $errors = array();

    // New name
    if (empty($_POST['name'])) {
        $errors[] = "Please enter a name";
     
    } else {
        $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    }
    //email
    if (empty($_POST['email'])) {
        $errors[] = "Please enter email.";
    } else {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    }
    // New password
    if (empty($_POST['password'])) {
        $errors[] = "Please enter a password.";
    } else {
        $password = mysqli_real_escape_string($conn, trim($_POST['password']));
    }

    if (empty($errors)) {

        $query = "INSERT INTO admin(name, email, password) VALUES( '$name', '$email', '$password' );";

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

<form action="admin_registration.php" method="POST">

    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Name</label><br>
    <input type="text" name="name" required ><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="SUBMIT" value="SUBMIT">

</form>

</body>
</html>