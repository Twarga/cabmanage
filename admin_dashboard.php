<?php
session_start();
if ($_SESSION['user_type'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Redirect admin to phpMyAdmin
header("Location: http://localhost/phpmyadmin");
exit;
