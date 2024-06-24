<?php
session_start();
if ($_SESSION['user_type'] !== 'Assistant') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistant Dashboard</title>
</head>
<body>
    <h2>Assistant Dashboard</h2>
    <a href="patient_management.php">Patient Management</a>
    <a href="prelevement_management.php">Prelevement Management</a>
    <a href="logout.php">Logout</a>
</body>
</html>
