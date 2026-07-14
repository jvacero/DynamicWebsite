<!DOCTYPE html>
<html>
<head>
    <title>Delete User</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php

if (isset($_POST['DELETE'])) {

    require('../config/mysqli_connect.php');

    $errors = array();

    
    if (empty($_POST['email'])) {
        $errors[] = "Please enter an email.";
    } else {
        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    }

    
    if (empty($errors)) {

        $query = "DELETE FROM admin WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_affected_rows($conn) == 1) {
            echo "<p style='color:green;'>Admin deleted successfully!</p>";
        } else {
            echo "<p style='color:red;'>No user found with that email.</p>";
        }

    } else {
        foreach ($errors as $msg) {
            echo "<p style='color:red;'>$msg</p>";
        }
    }

    mysqli_close($conn);
}

?>

<form action="admin_delete.php" method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>

    <input type="submit" name="DELETE" value="DELETE">
</form>

</body>
</html>