<?php
require './config/mysqli_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pixel Market</title>
    <link rel="stylesheet" href="assets/style.css" type="text/css">
    <link rel="stylesheet" href="assets/header.css" type="text/css">
    <link rel="stylesheet" href="assets/dashboard.css" type="text/css">
    <link rel="stylesheet" href="assets/footer.css" type="text/css">
</head>
<body>
    <?php include './includes/header.php'; ?>

    <main class="page-main">
        <?php include './includes/dashboard.php'; ?>
    </main>

    <?php include './includes/footer.php'; ?>
</body>
</html>