<?php

require_once "../config/mysqli_connect.php";
require_once "../config/session.php";

$email = trim($_POST['email']);
password_verify($password = $_POST['password']);

$sql = "SELECT * FROM user WHERE email=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {

        session_regenerate_id(true);

        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        header("Location: ../index.php");
        exit;
    }
}

echo "Invalid email or password.";