<!-- DONT TOUCH THIS - AUTOMATED QUERY TO PAG DI AVAILABLE YUNG DATABASE SA INYO LOCALLY -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Checker</title>
</head>
<body>

<?php

$host = "localhost";
$user = "root";
$password = "Password@123";
$database = "test";

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Connected to MySQL Server.<br><br>";

/* ============================================
   DATABASE
============================================ */

if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$database`")) {
    die("❌ Failed to create database: " . $conn->error);
}

echo "✅ Database '$database' is ready.<br>";

$conn->select_db($database);

/* ============================================
   TABLES
============================================ */

$conn->query("
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255)
)");

$conn->query("
CREATE TABLE IF NOT EXISTS test_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    age INT
)");

echo "✅ Tables are ready.<br>";

/* ============================================
   COLUMNS
============================================ */

$requiredColumns = [

    "admin" => [

        "id" => "INT AUTO_INCREMENT PRIMARY KEY",
        "name" => "VARCHAR(255)",
        "email" => "VARCHAR(255)",
        "password" => "VARCHAR(255)"

    ],

    "test_user" => [

        "id" => "INT AUTO_INCREMENT PRIMARY KEY",
        "name" => "VARCHAR(255)",
        "age" => "INT"

    ]

];

foreach ($requiredColumns as $table => $columns) {

    foreach ($columns as $column => $definition) {

        $result = $conn->query("
            SHOW COLUMNS
            FROM `$table`
            LIKE '$column'
        ");

        if ($result->num_rows == 0) {

            if ($column == "id") {

                // Skip id because CREATE TABLE already creates it
                continue;

            }

            $conn->query("
                ALTER TABLE `$table`
                ADD COLUMN `$column` $definition
            ");

            echo "➕ Added column '$column' to '$table'.<br>";

        } else {

            echo "✅ Column '$column' exists in '$table'.<br>";

        }

    }

}

/* ============================================
   INDEXES
============================================ */

$indexes = [

    "admin" => [

        "PRIMARY" => "PRIMARY KEY (`id`)",
        "email_idx" => "INDEX (`email`)"

    ],

    "test_user" => [

        "PRIMARY" => "PRIMARY KEY (`id`)"

    ]

];

foreach ($indexes as $table => $tableIndexes) {

    $existingIndexes = [];

    $result = $conn->query("SHOW INDEX FROM `$table`");

    while ($row = $result->fetch_assoc()) {

        $existingIndexes[] = $row['Key_name'];

    }

    foreach ($tableIndexes as $indexName => $definition) {

        if (!in_array($indexName, $existingIndexes)) {

            if ($indexName == "PRIMARY") {

                // PRIMARY already exists from CREATE TABLE
                continue;

            }

            $conn->query("
                ALTER TABLE `$table`
                ADD $definition
            ");

            echo "➕ Added index '$indexName' on '$table'.<br>";

        } else {

            echo "✅ Index '$indexName' exists on '$table'.<br>";

        }

    }

}

echo "<br><strong>✔ Database verification complete.</strong>";

$conn->close();

?>

<br><br>

<div>
    <a href="../admin/login_admin.php">Go Back</a>
    <a href="../admin/dashboard.php">Go to Dashboard</a>
</div>

</body>
</html>