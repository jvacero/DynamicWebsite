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



<script>
    $(document).ready(function () {
        $('#myTable').DataTable();
    });
</script> 

<div>
<table id="myTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Tiger Nixon</td>
            <td>System Architect</td>
            <td>Edinburgh</td>
            <td>61</td>
        </tr>
        <tr>
            <td>Garrett Winters</td>
            <td>Accountant</td>
            <td>Tokyo</td>
            <td>63</td>
        </tr>
    </tbody>
</table>   
</div>

<?php
    echo $_POST['email'];
    echo $_POST['password'];
    $conn = new mysqli("localhost","admin","Pass@123","test");
    if ($conn->connect_error){
        die("Connection Failed");
    }
    echo "connected". "<br>";

    $sql = "SELECT * FROM test_user ";
    $result = $conn->query($sql);

    while ($row = $result->fetch_assoc())
        {
            echo $row['id'];
            echo $row['name'];
            echo $row['age'] . "<br>";
        }
?>    
</body>
</html>