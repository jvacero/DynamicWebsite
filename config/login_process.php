<?php

require_once "../config/mysqli_connect.php";
require_once "../config/session.php";

$username = trim($_POST['username']);
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE email=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $username);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $admin['password'])) {

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        header("Location: dashboard.php");
        exit;
    }
}

echo "Invalid email or password.";