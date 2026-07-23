<?php
    $conn = new mysqli("localhost","root","Password@123","user");
    if ($conn->connect_error){
        die("Connection Failed");
    }
    echo "connected". "<br>";

    
?>
