<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Examen.php';
require_once 'Facture.php'; // Include the Facture class

// Initialize the Examen and Facture classes
$db = $link;
$examen = new Examen($db);
$facture = new Facture($db); // Initialize the Facture class

// Get the examen ID from the URL
$examen_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Examen ID not found.');

// Delete related factures first
if ($facture->deleteByExamenId($examen_id)) {
    // Delete the examen
    if ($examen->delete($examen_id)) {
        // Redirect to the examen management page
        header("Location: examen.php");
        exit;
    } else {
        die('Error deleting examen.');
    }
} else {
    die('Error deleting related factures.');
}
?>
