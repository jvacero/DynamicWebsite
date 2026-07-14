<?php
    $conn = new mysqli("localhost","root","","user");
    if ($conn->connect_error){
        die("Connection Failed");
    }
    echo "connected". "<br>";

    
?>