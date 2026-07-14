<?php

require_once "session.php";

if (!isset($_SESSION['id'])) {

    header("Location: login_admin.php");

    exit;
}