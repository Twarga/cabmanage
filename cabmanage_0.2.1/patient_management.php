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

// Fetch all patients sorted by creation date in descending order
$patients = $patient->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Management</title>
</head>
<body>
    <h2>Patient Management</h2>
    <a href="create_patient.php">Create New Patient</a>
    <a href='doctor_dashboard.php'>Back to Doctor Dashboard</a>

    <form method="post" action="patient_management.php">
        <label>Search:</label>
        <input type="text" name="search_term" placeholder="Enter name, ID, or date of birth">
        <button type="submit">Search</button>
    </form>
    <br>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Prenom</th>
            <th>Date Naissance</th>
            <th>Age</th>
            <th>Type Identification</th>
            <th>Identification Number</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Situation Familiale</th>
            <th>Sexe</th>
            <th>Adresse</th>
            <th>Type Assurance</th>
            <th>Numero Assurance</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($patients as $patient) { ?>
            <tr>
                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                <td><?php echo htmlspecialchars($patient['age']); ?></td>
                <td><?php echo htmlspecialchars($patient['type_identification']); ?></td>
                <td><?php echo htmlspecialchars($patient['identification_number']); ?></td>
                <td><?php echo htmlspecialchars($patient['email']); ?></td>
                <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                <td><?php echo htmlspecialchars($patient['situation_familiale']); ?></td>
                <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                <td><?php echo htmlspecialchars($patient['adresse']); ?></td>
                <td><?php echo htmlspecialchars($patient['type_assurance']); ?></td>
                <td><?php echo htmlspecialchars($patient['numero_assurance']); ?></td>
                <td>
                    <a href="edit_patient.php?id=<?php echo $patient['patient_id']; ?>">Edit</a>
                    <a href="delete_patient.php?id=<?php echo $patient['patient_id']; ?>" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
                    <a href="view_patient_assistance.php?id=<?php echo $patient['patient_id']; ?>">View</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
