<?php
session_start();
if ($_SESSION['user_type'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <a href="patient_management.php">Patient Management</a>
    <a href="logout.php">Logout</a>
</body>
</html>
