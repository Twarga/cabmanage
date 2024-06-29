<!-- prelevement_management_assistant.php -->
<?php
session_start();
require_once 'config.php';
require_once 'Patient.php';

$db = $link;
$patient = new Patient($db);

$patients = $patient->read();

if (isset($_GET['search'])) {
    $search_term = $search_term;
    $patients = $patient->search($search_term);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prelevement Management for Assistants</title>
</head>
<body>
    <h2>Prelevement Management</h2>
    
    <form method="get" action="prelevement_management_assistant.php">
        <input type="text" name="search" placeholder="Search Patients">
        <button type="submit">Search</button>
    </form>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Prenom</th>
            <th>Date Naissance</th>
            <th>Identification Number</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($patients as $patient) : ?>
            <tr>
                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                <td><?php echo htmlspecialchars($patient['identification_number']); ?></td>
                <td>
                    <a href="create_prelevement_assistant.php?patient_id=<?php echo $patient['patient_id']; ?>">Select</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
