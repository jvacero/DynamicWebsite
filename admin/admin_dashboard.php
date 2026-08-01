<!DOCTYPE html>
<html lang="en">
<head>
    <!-- scripts -->
     
    <!-- CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />

<!-- jQuery (Required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>   

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
    require_once __DIR__ . '/../config/mysqli_connect.php';
    include '../config/auth.php';
?>



<script>
    $(document).ready(function () {
        $('#userTable').DataTable();
    });
</script>

<div>
<table id="userTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Admin</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $userSql = "SELECT id, name, email, username, admin FROM user";
        $userResult = $conn->query($userSql);

        if ($userResult && $userResult->num_rows > 0) {
            while ($user = $userResult->fetch_assoc()) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo $user['admin'] ? 'Yes' : 'No'; ?></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr>
                <td colspan="4">No users found.</td>
            </tr>
            <?php
        }
        ?>
    </tbody>
</table>   
</div>

<div>
    <a href="../index.php">Go Back</a>
</div>
</body>
</html>