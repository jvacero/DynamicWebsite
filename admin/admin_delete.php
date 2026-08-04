<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link rel="stylesheet" type="text/css" href="../assets/admin_delete.css">
</head>
<body>
    <div class="delete-page">
        <h2>Delete User</h2>
        <p>Enter an email and/or username to remove a user account.</p>

        <?php
        if (isset($_POST['DELETE'])) {
            require_once __DIR__ . '/../config/mysqli_connect.php';

            $errors = [];
            $email = trim($_POST['email'] ?? '');
            $username = trim($_POST['username'] ?? '');

            if (empty($email) && empty($username)) {
                $errors[] = 'Please enter an email or username.';
            }

            if (empty($errors)) {
                if (!empty($email) && !empty($username)) {
                    $query = 'DELETE FROM user WHERE email = ? OR username = ?';
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('ss', $email, $username);
                } elseif (!empty($email)) {
                    $query = 'DELETE FROM user WHERE email = ?';
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('s', $email);
                } else {
                    $query = 'DELETE FROM user WHERE username = ?';
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param('s', $username);
                }

                $stmt->execute();

                if ($stmt->affected_rows === 1) {
                    echo "<p class='success'>User deleted successfully.</p>";
                } else {
                    echo "<p class='error'>No matching user found.</p>";
                }

                $stmt->close();
            } else {
                foreach ($errors as $msg) {
                    echo "<p class='error'>$msg</p>";
                }
            }

            $conn->close();
        }
        ?>

        <form action="admin_delete.php" method="POST" class="delete-form">
            <label for="deleteEmail">Email</label>
            <input id="deleteEmail" type="email" name="email" placeholder="Enter email">

            <label for="deleteUsername">Username</label>
            <input id="deleteUsername" type="text" name="username" placeholder="Enter username">

            <button type="submit" name="DELETE" value="DELETE">Delete User</button>
        </form>

        <a href="admin_dashboard.php" class="back-link">Back to dashboard</a>
    </div>
</body>
</html>