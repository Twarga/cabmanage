<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';
require_once 'Prelevement.php';

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Patient ID not found.');

// Fetch patient data
$patient_data = $patient->readOne($patient_id);

// Fetch prélèvements for the patient
$prelevements = $prelevement->readByPatient($patient_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patient</title>
</head>
<body>
    <h2>View Patient</h2>
    <table border="1">
        <tr><td>Name</td><td><?php echo htmlspecialchars($patient_data['name']); ?></td></tr>
        <tr><td>Prenom</td><td><?php echo htmlspecialchars($patient_data['prenom']); ?></td></tr>
        <tr><td>Date Naissance</td><td><?php echo htmlspecialchars($patient_data['date_naissance']); ?></td></tr>
        <tr><td>Age</td><td><?php echo htmlspecialchars($patient_data['age']); ?></td></tr>
        <tr><td>Type Identification</td><td><?php echo htmlspecialchars($patient_data['type_identification']); ?></td></tr>
        <tr><td>Identification Number</td><td><?php echo htmlspecialchars($patient_data['identification_number']); ?></td></tr>
        <tr><td>Email</td><td><?php echo htmlspecialchars($patient_data['email']); ?></td></tr>
        <tr><td>Phone Number</td><td><?php echo htmlspecialchars($patient_data['phone_number']); ?></td></tr>
        <tr><td>Situation Familiale</td><td><?php echo htmlspecialchars($patient_data['situation_familiale']); ?></td></tr>
        <tr><td>Sexe</td><td><?php echo htmlspecialchars($patient_data['sexe']); ?></td></tr>
        <tr><td>Adresse</td><td><?php echo htmlspecialchars($patient_data['adresse']); ?></td></tr>
        <tr><td>Type Assurance</td><td><?php echo htmlspecialchars($patient_data['type_assurance']); ?></td></tr>
        <tr><td>Numero Assurance</td><td><?php echo htmlspecialchars($patient_data['numero_assurance']); ?></td></tr>
    </table>

    <h3>Prélèvements</h3>
    <table border="1">
        <tr>
            <th>ID Prélèvement</th>
            <th>Type Prélèvement</th>
            <th>Date Réception</th>
            <th>Date Création</th>
            <th>Nombre de flacons</th>
            <th>Docteur Exterieur</th>
            <th>Rapport</th>
        </tr>
        <?php foreach ($prelevements as $prelevement) : ?>
            <tr>
                <td><?php echo htmlspecialchars($prelevement['prelevement_id']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['type_prelevement']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['date_reception']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['date_creation']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['nombre_flacons']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['docteur_exterieur_id']); ?></td>
                <td><?php echo htmlspecialchars($prelevement['rapport_txt']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="patient_management.php">Back to Patient Management</a>
</body>
</html>
