<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';

// Initialize the Prelevement class
$db = $link;
$prelevement = new Prelevement($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);

if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Output the details for printing or generate a PDF
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Prelevement</title>
</head>
<body>
    <h2>Prelevement Details</h2>
    <table border="1">
        <tr><td>Prelevement ID</td><td><?php echo htmlspecialchars($prelevement_data['prelevement_id']); ?></td></tr>
        <tr><td>Type</td><td><?php echo htmlspecialchars($prelevement_data['type_prelevement']); ?></td></tr>
        <tr><td>Date Reception</td><td><?php echo htmlspecialchars($prelevement_data['date_reception']); ?></td></tr>
        <tr><td>Date Creation</td><td><?php echo htmlspecialchars($prelevement_data['date_creation']); ?></td></tr>
        <tr><td>Nombre de Flacons</td><td><?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?></td></tr>
        <tr><td>Docteur Exterieur</td><td><?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?></td></tr>
        <tr><td>Rapport Template</td><td><?php echo htmlspecialchars($prelevement_data['rapport_template']); ?></td></tr>
        <tr><td>Rapport Text</td><td><?php echo htmlspecialchars($prelevement_data['rapport_txt']); ?></td></tr>
    </table>
    <button onclick="window.print()">Print</button>
</body>
</html>
