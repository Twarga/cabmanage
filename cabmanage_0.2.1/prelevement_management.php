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

// Fetch all patients
$patients = $patient->read();

// Fetch search results if search term is provided
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $patients = $patient->search($search_term);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prélèvement Liste</title>
    <link rel="stylesheet" href="Front/lstprelevment.css">
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Front\imag\logo.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="logo-section">
                <img src="Front/imag/logo.png" alt="Laboratory Logo" class="logo">
            </div>
            <div class="nav-buttons">
                <button class="nav-button" onclick="location.href='statistics.php'">Tableau de bord</button>
                <button class="nav-button" onclick="location.href='patient_management.php'">Patient</button>
                <div class="user-section">
                    <img src="Front/imag/doc.jpeg" alt="User Icon" class="user-icon">
                </div>
                <button class="nav-button" onclick="location.href='prelevement_management.php'">Prélèvement</button>
                <button class="nav-button" onclick="location.href='examen.php'">Examen</button>
            </div>
            <div class="btn-logout">
                <button class="btn-logout" onclick="location.href='logout.php'"><img src="Front/imag/logout.png" alt="Logout Button Icon"></button>
            </div>
        </div>
        <div class="content-area">
            <button id="retour" class="back-button" onclick="location.href='doctor_dashboard.php'">
                <i class="fas fa-arrow-left"><img src="Front/imag/left-arrow.png"></i>
            </button>
            <h1>Prélèvement Liste</h1>
            <div class="search-container">
                <form method="get" action="prelevement_management.php" class="search-form">
                    <input type="text" name="search" placeholder="Cherche" class="search-input" value="<?php echo htmlspecialchars($search_term ?? ''); ?>">
                    <button type="submit" class="search-btn"><img src="Front/imag/searching.png" alt="Search"></button>
                    <button type="button" class="codbar-btn"><img src="Front/imag/barcode-scanner.png" alt="Barcode"></button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Sexe</th>
                        <th>Date de naissance</th>
                        <th>CIN</th>
                        <th>Telephone</th>
                        <th>Situation Familiale</th>
                        <th>Assurance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                            <td><?php echo htmlspecialchars($patient['name']); ?></td>
                            <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                            <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                            <td><?php echo htmlspecialchars($patient['identification_number']); ?></td>
                            <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($patient['situation_familiale']); ?></td>
                            <td><?php echo htmlspecialchars($patient['type_assurance']); ?></td>
                            <td>
                                <button class="add-btn" onclick="location.href='create_prelevement.php?patient_id=<?php echo $patient['patient_id']; ?>'"><img src="Front/imag/add.png" alt="add"></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
