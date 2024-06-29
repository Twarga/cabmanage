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
    <ul>
        <li><a href="patient_management.php">Patient Management</a></li>
        <li><a href="prelevement_management.php">Prelevement Management</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>
