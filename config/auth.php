<?php

require_once "session.php";

if (!isset($_SESSION['id'])) {

    header("Location: admin_login.php");

    exit;
}