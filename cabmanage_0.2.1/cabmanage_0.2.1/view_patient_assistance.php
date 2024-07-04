<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';
require_once 'Prelevement.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);
$facture = new Facture($db);

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Patient ID not found.');

// Fetch patient data
$patient_data = $patient->readOne($patient_id);

// Fetch prelevement history for the patient
$prelevements_history = $prelevement->readByPatient($patient_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patient</title>
    <script>
        function confirmDelete(prelevement_id, patient_id) {
            if (confirm('Are you sure you want to delete this prelevement?')) {
                window.location.href = 'delete_prelevement_view.php?id=' + prelevement_id + '&patient_id=' + patient_id + '&source=assistance';
            }
        }
    </script>
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

    <h3>Prelevement History</h3>
    <table border="1">
        <tr>
            <th>Prelevement ID</th>
            <th>Type</th>
            <th>Date Reception</th>
            <th>Date Creation</th>
            <th>Nombre de Flacons</th>
            <th>Docteur Exterieur</th>
            <th>Facture Etat</th>
            <th>Rest</th>
            <th>Ordonnance</th>
            <th>Edit</th>
            <th>Delete</th>
            <th>Imprime</th>
        </tr>
        <?php foreach ($prelevements_history as $history): 
            $facture_data = $facture->readOne($history['prelevement_id']); ?>
            <tr>
                <td><?php echo htmlspecialchars($history['prelevement_id']); ?></td>
                <td><?php echo htmlspecialchars($history['type_prelevement']); ?></td>
                <td><?php echo htmlspecialchars($history['date_reception']); ?></td>
                <td><?php echo htmlspecialchars($history['date_creation']); ?></td>
                <td><?php echo htmlspecialchars($history['nombre_flacons']); ?></td>
                <td><?php echo htmlspecialchars($history['docteur_exterieur_id']); ?></td>
                <td><?php echo htmlspecialchars($facture_data['etat_paiement'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($facture_data['rest'] ?? 'N/A'); ?></td>
                <td><?php echo $history['ordonnance'] ? '<a href="download_ordonance.php?id=' . $history['prelevement_id'] . '">Download</a>' : 'No Ordonnance'; ?></td>
                <td><a href="edit_prelevement_assistant.php?id=<?php echo $history['prelevement_id']; ?>">Edit</a></td>
                <td><a href="javascript:confirmDelete(<?php echo $history['prelevement_id']; ?>, <?php echo $patient_id; ?>)">Delete</a></td>
                <td><a href="print_prelevement.php?id=<?php echo $history['prelevement_id']; ?>">Imprime</a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="patient_management_assistance.php">Back to Patient Management</a>
</body>
</html>
