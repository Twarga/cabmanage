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
    <li><a href="statistics.php">Statistics</a></li>
    <li><a href="patient_management_assistance.php">Patient Management</a></li>
    <li><a href="prelevement_management_assistant.php">Prelevement Management</a></li>
    <li><a href="logout.php">Logout</a></li>
</body>
</html>
