<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);

// Get the prelevement ID and patient ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : die('ERROR: Patient ID not found.');
$source = isset($_GET['source']) ? $_GET['source'] : 'view'; // default to 'view'

// Delete related facture first
$facture_data = $facture->readOne($prelevement_id);
if ($facture_data) {
    if (!$facture->deleteByPrelevement($prelevement_id)) {
        die('Error: Could not delete related facture.');
    }
}

// Delete the prelevement
if ($prelevement->delete($prelevement_id)) {
    if ($source == 'assistance') {
        header("Location: view_patient_assistance.php?id=" . $patient_id);
    } else {
        header("Location: view_patient.php?id=" . $patient_id);
    }
    exit;
} else {
    die('Error deleting prelevement.');
}
?>
