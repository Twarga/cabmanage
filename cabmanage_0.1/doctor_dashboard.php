<?php
session_start();
if ($_SESSION['user_type'] !== 'Docteur') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
</head>
<body>
    <h2>Doctor Dashboard</h2>
    <a href="patient_management.php">Patient Management</a>
    <a href="prelevement_management.php">Patient Management</a>
    <a href="logout.php">Logout</a>
</body>
</html>
