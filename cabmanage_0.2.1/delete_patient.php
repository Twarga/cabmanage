<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';

// Initialize the Patient class
$db = $link;
$patient = new Patient($db);

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Patient ID not found.');

// Delete the patient
if ($patient->delete($patient_id)) {
    header("Location: patient_management.php");
    exit;
} else {
    echo "Error deleting patient.";
}
?>
