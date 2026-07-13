<!DOCTYPE html>
<html lang="en">
<head>
    <!-- script -->
     
    <script rel="stylesheet" href="//cdn.datatables.net/2.3.8/css/dataTables.dataTables.min.css"></script>
    <script rel="stylesheet" href="//cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>
    <!-- 2. Load DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php 
$conn = new mysqli("localhost", "admin", "Pass@123", "test");

if ($conn -> connect_error) {
    die("connection_error");
}

echo "connected";
?>

<div>
<table ></table>
</div>

<script>
$(document).ready(function() {
    $('#test').DataTable();
});
</script>

    
</body>
</html>