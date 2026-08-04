<?php
require_once __DIR__ . '/../config/mysqli_connect.php';
require_once __DIR__ . '/../config/auth.php';

$uploadDirectory = __DIR__ . '/../uploads';
if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

$message = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_item'])) {
    $productName = trim($_POST['productname'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = trim($_POST['stock'] ?? '');

    if ($productName === '') {
        $errorMessage = 'Product name is required.';
    } elseif ($price === '' || !is_numeric($price)) {
        $errorMessage = 'Please enter a valid price.';
    } elseif ($stock === '' || !ctype_digit($stock)) {
        $errorMessage = 'Please enter a valid stock quantity.';
    } elseif (!isset($_FILES['productimage']) || $_FILES['productimage']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'Please upload an image.';
    } else {
        $originalName = basename($_FILES['productimage']['name']);
        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $imageName = time() . '_' . $safeName;
        $targetPath = $uploadDirectory . '/' . $imageName;

        if (!move_uploaded_file($_FILES['productimage']['tmp_name'], $targetPath)) {
            $errorMessage = 'Failed to save the uploaded image.';
        } else {
            $insertStmt = $conn->prepare('INSERT INTO product (productname, price, stock) VALUES (?, ?, ?)');
            $insertStmt->bind_param('sdi', $productName, $price, $stock);

            if ($insertStmt->execute()) {
                $productId = $insertStmt->insert_id;
                $insertStmt->close();

                $imageStmt = $conn->prepare('UPDATE product SET productimage = ? WHERE id = ?');
                $imageStmt->bind_param('si', $imageName, $productId);
                $imageStmt->execute();
                $imageStmt->close();

                $message = 'Item uploaded successfully.';
            } else {
                $errorMessage = 'Failed to save the product.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Product</title>
    <link rel="stylesheet" type="text/css" href="../assets/admin_update.css">
</head>
<body>
    <div class="update-container">
        <h2>Upload Product</h2>

        <?php if ($message !== ''): ?>
            <p class="message success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <p class="message error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form action="admin_up_item.php" method="POST" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="productname" required>

            <label>Price</label>
            <input type="number" step="0.01" name="price" required>

            <label>Stock</label>
            <input type="number" name="stock" required>

            <label>Product Image</label>
            <input type="file" name="productimage" accept="image/*" required>

            <button type="submit" name="upload_item">Upload Item</button>

            <button type="button" class="back-link" onclick="window.location.href='admin_dashboard.php'">Back to Admin Dashboard</button>
        </form>

    </div>
</body>
</html>
