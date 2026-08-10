<!DOCTYPE html>
<html lang="en">
<head>
    <!-- scripts -->
     
    <!-- CSS -->
    <link rel="stylesheet" href="../assets/admin_dashboard.css" type="text/css" media="screen">
    <link rel="stylesheet" href="../assets/admin_footer.css" type="text/css" media="screen">
    <link rel="stylesheet" href="../assets/admin_update.css" type="text/css" media="screen">
    <link rel="stylesheet" href="../assets/admin_delete.css" type="text/css" media="screen">
    <link rel="stylesheet" href="../assets/admin_header.css" type="text/css" media="screen">
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

<header><?php
     include '../admin/admin_header.php';
     $activitySql = "
    SELECT * FROM (
        SELECT
            u.username AS username,
            p.productname AS item_name,
            c.quantity AS quantity,
            'In Cart' AS activity_type,
            '' AS reference_number,
            c.added_at AS activity_date
        FROM cart c
        JOIN user u ON u.id = c.user_id
        JOIN product p ON p.id = c.product_id

        UNION ALL

        SELECT
            u.username AS username,
            p.productname AS item_name,
            oi.quantity AS quantity,
            CONCAT('Bought (', oh.status, ')') AS activity_type,
            oh.order_reference AS reference_number,
            oh.created_at AS activity_date
        FROM order_items oi
        JOIN order_history oh ON oh.id = oi.order_id
        JOIN user u ON u.id = oh.user_id
        JOIN product p ON p.id = oi.product_id
    ) AS activity_table
    ORDER BY activity_date DESC
";
    ?></header>

<div>
    
</div>


<?php
    require_once __DIR__ . '/../config/mysqli_connect.php';
    require_once __DIR__ . '/../config/auth.php';
    enforce_admin_access($conn);

    $updateMessage = '';
    $updateStatus = '';
    $deleteMessage = '';
    $deleteStatus = '';
    $uploadMessage = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['UPDATE_ADMIN'])) {
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
                    $updateStatus = 'success';
                    $updateMessage = 'Admin details updated successfully.';
                } else {
                    $updateStatus = 'error';
                    $updateMessage = 'Unable to update admin details.';
                }
            } else {
                $updateStatus = 'error';
                $updateMessage = 'No changes were applied.';
            }
        } else {
            $updateStatus = 'error';
            $updateMessage = implode('<br>', $errors);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['DELETE_ADMIN'])) {
        $deleteErrors = [];
        $deleteEmail = trim($_POST['delete_email'] ?? '');
        $deleteUsername = trim($_POST['delete_username'] ?? '');

        if ($deleteEmail === '' && $deleteUsername === '') {
            $deleteErrors[] = 'Please enter an email or username.';
        }

        if (empty($deleteErrors)) {
            if ($deleteEmail !== '' && $deleteUsername !== '') {
                $deleteQuery = 'DELETE FROM user WHERE email = ? OR username = ?';
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param('ss', $deleteEmail, $deleteUsername);
            } elseif ($deleteEmail !== '') {
                $deleteQuery = 'DELETE FROM user WHERE email = ?';
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param('s', $deleteEmail);
            } else {
                $deleteQuery = 'DELETE FROM user WHERE username = ?';
                $deleteStmt = $conn->prepare($deleteQuery);
                $deleteStmt->bind_param('s', $deleteUsername);
            }

            $deleteStmt->execute();

            if ($deleteStmt->affected_rows === 1) {
                $deleteStatus = 'success';
                $deleteMessage = 'User deleted successfully.';
            } else {
                $deleteStatus = 'error';
                $deleteMessage = 'No matching user found.';
            }

            $deleteStmt->close();
        } else {
            $deleteStatus = 'error';
            $deleteMessage = implode('<br>', $deleteErrors);
        }
    }

?>

<div id="adminUpdateModal" class="modal-overlay" onclick="closeAdminUpdateModal(event)">
    <div class="modal-window" onclick="event.stopPropagation()">
        <button type="button" class="modal-close" onclick="closeAdminUpdateModal()">×</button>

        <?php if ($updateMessage !== ''): ?>
            <p class="update-message <?php echo htmlspecialchars($updateStatus); ?>"><?php echo $updateMessage; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="old_email">Current Email</label>
            <br>
            <input type="email" id="old_email" name="old_email" required>
            <br><br>
            <label for="name">New Name    </label>
            <br>
            <input type="text" id="name" name="name">
            <br><br>
            <label for="username">New Username</label>
            <br>
            <input type="text" id="username" name="username">
            <br>
            <p>-</p>
            <label class="checkbox-row">
                <br>
                <input type="checkbox" name="skip_username">
                <span>Do not update username</span>
            </label>
            <br>

            <label for="password">New Password</label>
            <br>
            <input type="password" id="password" name="password">

            <button type="submit" name="UPDATE_ADMIN" value="1" class="submit-btn">Update</button>
        </form>
    </div>
</div>

<div id="adminDeleteModal" class="modal-overlay" onclick="closeAdminDeleteModal(event)">
    <div class="modal-window" onclick="event.stopPropagation()">
        <button type="button" class="modal-close" onclick="closeAdminDeleteModal()">×</button>

        <?php if ($deleteMessage !== ''): ?>
            <p class="delete-message <?php echo htmlspecialchars($deleteStatus); ?>"><?php echo $deleteMessage; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="delete_email">Email</label>
            <br>
            <input type="email" id="delete_email" name="delete_email">
            <br><br>
            <label for="delete_username">Username</label>
            <br>
            <input type="text" id="delete_username" name="delete_username">
            
            <button type="submit" name="DELETE_ADMIN" value="1" class="submit-btn">Delete</button>
        </form>
    </div>
</div>

<div id="adminUploadModal" class="modal-overlay" onclick="closeAdminUploadModal(event)">
    <div class="modal-window" onclick="event.stopPropagation()">
        <button type="button" class="modal-close" onclick="closeAdminUploadModal()">×</button>

        <?php if ($uploadMessage !== ''): ?>
            <p class="upload-message <?php echo htmlspecialchars($deleteStatus); ?>"><?php echo $uploadMessage; ?></p>
        <?php endif; ?>

        <form action="admin_up_item.php" method="POST" enctype="multipart/form-data">
            <label>Product Name</label><br>
            <input type="text" name="productname" required><br><br>

            <label>Price</label><br>
            <input type="number" step="0.01" name="price" required><br><br>

            <label>Stock</label><br>
            <input type="number" name="stock" required><br><br>

            <label>Product Image</label><br>
            <input type="file" name="productimage" accept="image/*" required><br><br>

            <button type="submit" name="upload_item" class="submit-btn">
                Upload Item
            </button>
        </form>
    </div>
</div>

<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the admin dashboard. Here you can manage users and view statistics.</p>

    <div class="section-card">
        <h2>User Management</h2>
        <p>Below is a list of all registered users:</p>
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

    <div class="section-card">
        <h2>User Item Activity</h2>
        <p>Recent cart items and purchase history:</p>
        <table id="activityTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Activity</th>
                    <th>Reference</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $activitySql = "
                    SELECT
                        u.username AS username,
                        p.productname AS item_name,
                        c.quantity AS quantity,
                        'In Cart' AS activity_type,
                        '' AS reference_number,
                        c.added_at AS activity_date
                    FROM cart c
                    JOIN user u ON u.id = c.user_id
                    JOIN product p ON p.id = c.product_id

                    UNION ALL

                    SELECT
                        u.username AS username,
                        p.productname AS item_name,
                        oi.quantity AS quantity,
                        CONCAT('Bought (', oh.status, ')') AS activity_type,
                        oh.order_reference AS reference_number,
                        oh.created_at AS activity_date
                    FROM order_items oi
                    JOIN order_history oh ON oh.id = oi.order_id
                    JOIN user u ON u.id = oh.user_id
                    JOIN product p ON p.id = oi.product_id
                    ORDER BY activity_date DESC
                ";
                $activityResult = $conn->query($activitySql);

                if ($activityResult && $activityResult->num_rows > 0) {
                    while ($activity = $activityResult->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($activity['username']); ?></td>
                            <td><?php echo htmlspecialchars($activity['item_name']); ?></td>
                            <td><?php echo (int)$activity['quantity']; ?></td>
                            <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                            <td><?php echo htmlspecialchars($activity['reference_number']); ?></td>
                            <td><?php echo htmlspecialchars($activity['activity_date']); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6">No activity found.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function openAdminUploadModal() {
    document.getElementById('adminUploadModal').classList.add('active');
}

function closeAdminUploadModal(event) {
    if (event && event.target.id !== 'adminUploadModal') {
        return;
    }

    document.getElementById('adminUploadModal').classList.remove('active');
}

function openAdminUpdateModal() {
    document.getElementById('adminUpdateModal').classList.add('active');
}

function closeAdminUpdateModal(event) {
    if (event && event.target.id !== 'adminUpdateModal') {
        return;
    }
    document.getElementById('adminUpdateModal').classList.remove('active');
}

function openAdminDeleteModal() {
    document.getElementById('adminDeleteModal').classList.add('active');
}

function closeAdminDeleteModal(event) {
    if (event && event.target.id !== 'adminDeleteModal') {
        return;
    }
    document.getElementById('adminDeleteModal').classList.remove('active');
}

$(document).ready(function () {
    $('#userTable').DataTable();
    $('#activityTable').DataTable();
});
</script>

</body>
</html>

<footer>
    <?php include '../includes/footer.php'; ?>
</footer>