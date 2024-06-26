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

// Get prelevement ID from URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data to get the patient_id
$prelevement_data = $prelevement->readOne($prelevement_id);
$patient_id = $prelevement_data['patient_id'];

if (!$patient_id) {
    die('ERROR: Patient ID not found.');
}

// Delete the prelevement
if ($prelevement->delete($prelevement_id)) {
    header("Location: create_prelevement.php?patient_id={$patient_id}");
    exit;
} else {
    echo "Error deleting prelevement.";
}
?>
