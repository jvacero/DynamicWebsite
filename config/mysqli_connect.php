<?php
    $conn = new mysqli("localhost","root","","test");
    if ($conn->connect_error){
        die("Connection Failed");
    }
    echo "connected". "<br>";

    
?>