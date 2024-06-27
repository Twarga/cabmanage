<?php
// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';

// Initialize the Patient class
$db = $link;
$patient = new Patient($db);

// Get the patient ID from the query string
$patient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the patient details
$patient_data = $patient->readOne($patient_id);

if (!$patient_data) {
    echo "Patient not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile</title>
</head>
<body>
    <h2>Patient Profile</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($patient_data['name']); ?></p>
    <p><strong>Prenom:</strong> <?php echo htmlspecialchars($patient_data['prenom']); ?></p>
    <p><strong>Date Naissance:</strong> <?php echo htmlspecialchars($patient_data['date_naissance']); ?></p>
    <p><strong>Age:</strong> <?php echo htmlspecialchars($patient_data['age']); ?></p>
    <p><strong>Type Identification:</strong> <?php echo htmlspecialchars($patient_data['type_identification']); ?></p>
    <p><strong>Identification Number:</strong> <?php echo htmlspecialchars($patient_data['identification_number']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($patient_data['email']); ?></p>
    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($patient_data['phone_number']); ?></p>
    <p><strong>Situation Familiale:</strong> <?php echo htmlspecialchars($patient_data['situation_familiale']); ?></p>
    <p><strong>Sexe:</strong> <?php echo htmlspecialchars($patient_data['sexe']); ?></p>
    <p><strong>Adresse:</strong> <?php echo htmlspecialchars($patient_data['adresse']); ?></p>
    <p><strong>Prelevement History:</strong> <?php echo htmlspecialchars($patient_data['prelevement_history']); ?></p>
    <p><strong>Type Assurance:</strong> <?php echo htmlspecialchars($patient_data['type_assurance']); ?></p>
    <p><strong>Numero Assurance:</strong> <?php echo htmlspecialchars($patient_data['numero_assurance']); ?></p>
</body>
</html>
