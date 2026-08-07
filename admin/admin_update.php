<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Admin</title>
    <link rel="stylesheet" type="text/css" href="../assets/admin_update.css">
</head>
<body>

<?php
require_once __DIR__ . '/../config/mysqli_connect.php';
require_once __DIR__ . '/../config/auth.php';
enforce_admin_access($conn);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['UPDATE'])) {
    $errors = [];

    $oldEmail = trim($_POST['old_email'] ?? '');
    if ($oldEmail === '') {
        $errors[] = 'Please enter the current email.';
    } else {
        $oldEmail = mysqli_real_escape_string($conn, $oldEmail);
    }

    $newName = trim($_POST['name'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');
    $skipUsername = isset($_POST['skip_username']);

    if ($newName === '' && $newUsername === '' && $newPassword === '') {
        $errors[] = 'Please enter at least one field to update.';
    }

    if (empty($errors)) {
        $tableName = 'admin';
        $tableCheck = $conn->query("SHOW TABLES LIKE 'admin'");
        if ($tableCheck && $tableCheck->num_rows === 0) {
            $tableName = 'user';
        }

        $updates = [];
        if ($newName !== '') {
            $updates[] = "name = '" . mysqli_real_escape_string($conn, $newName) . "'";
        }
        if (!$skipUsername && $newUsername !== '') {
            $updates[] = "username = '" . mysqli_real_escape_string($conn, $newUsername) . "'";
        }
        if ($newPassword !== '') {
            $updates[] = "password = '" . mysqli_real_escape_string($conn, $newPassword) . "'";
        }

        if (!empty($updates)) {
            $query = "UPDATE `$tableName` SET " . implode(', ', $updates) . " WHERE email = '$oldEmail'";
            $result = $conn->query($query);

            if ($result && $conn->affected_rows >= 0) {
                $message = 'Admin updated successfully.';
                $messageType = 'success';
            } else {
                $message = 'No matching admin found or no changes made.';
                $messageType = 'error';
            }
        } else {
            $message = 'No changes were applied.';
            $messageType = 'error';
        }
    } else {
        $message = implode('<br>', $errors);
        $messageType = 'error';
    }
}
?>

<div class="update-container">
    <h2>Update Admin</h2>

    <?php if ($message !== ''): ?>
        <p class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="admin_update.php" method="POST">
        <label for="old_email">Current Email</label>
        <input type="email" id="old_email" name="old_email" required>
        <label for="name">New Name</label>
        <input type="text" id="name" name="name">

        <label for="username">New Username</label>
        <input type="text" id="username" name="username">

        <label class="checkbox-row">
            <input type="checkbox" name="skip_username">
            <span>Do not update username</span>
        </label>

        <label for="password">New Password</label>
        <input type="password" id="password" name="password">

        <input type="submit" name="UPDATE" value="UPDATE">
    </form>
</div>

</body>
</html>