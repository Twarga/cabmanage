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

// Handle search form submission
$search_term = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['search_term'])) {
    $search_term = $_POST['search_term'];
    $patients = $patient->search($search_term);
} else {
    // Fetch all patients sorted by creation date in descending order
    $patients = $patient->readAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient</title>
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Front/imag/logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1F4D5A;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Hide the overall scrollbar */
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .content-area {
            flex: 1;
            width: 90%;
            margin-top: 20px;
            padding: 20px;
            background-color: #088696;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            text-align: center;
        }

        #retour {
            border: none;
            cursor: pointer;
            background: transparent;
            border-radius: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        #retour i {
            margin-right: 0px;
        }

        #retour img {
            width: 30px;
            height: 30px;
            margin-right: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between; /* Adjusted to move the title to the right */
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            text-align: left;
            font-size: 24px;
            color: #00E6FF;
        }

        .search-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
        }

        .search-container input[type="text"] {
            width: 70%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            flex-grow: 1; /* Makes the input take available space */
        }

        .new-patient-btn {
            padding: 10px 20px;
            border: none;
            background: linear-gradient(90deg, #00E6FF, #00B1FF);
            color: #000;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            margin-left: 10px; /* Adds some space between buttons */
        }

        .new-patient-btn img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .search-btn, .codbar-btn {
            padding: 10px 20px;
            border: none;
            background-color: transparent;
            color: #000;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            border-radius: 5px;
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        .search-btn img, .codbar-btn img {
            width: 40px;
            height: 40px;
            margin-right: -15px;
        }

        .table-container {
            height: calc(100vh - 300px); /* Adjust this value to fit the rest of the content */
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
        }

        th, td {
            padding: 10px;
            border: 0px solid #ddd;
            text-align: center;
        }

        th {
            background-color:#00E6FF;
            color: rgb(14, 13, 13);
        }

        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td {
            color: #000; /* Different color for the text in the data table */
        }

        .view-btn, .edit-btn, .delete-btn {
            padding: 5px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .view-btn img, .edit-btn img, .delete-btn img {
            width: 20px;
            height: 20px;
        }
        h1 {
            margin-bottom: 10px;
            font-size: x-large;
            color: #00E6FF;
            text-align: left;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .search-container input[type="text"] {
                width: 60%;
            }

            .search-btn, .new-patient-btn {
                padding: 10px;
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 100%;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header h1 {
                margin-bottom: 10px;
            }

            .search-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-container input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .search-btn, .new-patient-btn {
                width: 100%;
                padding: 10px;
                font-size: 14px;
                margin-bottom: 10px;
            }

            th, td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="logo-section">
                <img src="Front/imag/logo.png" alt="Laboratory Logo" class="logo">
            </div>
            <div class="nav-buttons">
                <button class="nav-button" onclick="location.href='doctor_dashboard.php'">Tableau de bord</button>
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
            <button id="retour" onclick="window.history.back()">
                <i class="fas fa-arrow-left"><img src="Front/imag/left-arrow.png"></i>
            </button>
            <h1>Patient Liste</h1>
            <div class="search-container">
                <form method="post" action="patient_management.php" style="display: flex; width: 100%;">
                    <input type="text" name="search_term" placeholder="Cherche" value="<?php echo htmlspecialchars($search_term); ?>">
                    <button type="submit" class="search-btn"><img src="Front/imag/searching.png" alt="Search"></button>
                </form>
                <button class="codbar-btn"><img src="Front/imag/barcode-scanner.png" alt="Search"></button> 
                <button class="new-patient-btn" onclick="location.href='create_patient.php'"><img src="Front/imag/add.png" alt="New Patient"> Nouveau Patient</button>
            </div>
            <div class="table-container">
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
                        <?php foreach ($patients as $patient) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                <td><?php echo htmlspecialchars($patient['name'] . ' ' . $patient['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($patient['sexe']); ?></td>
                                <td><?php echo htmlspecialchars($patient['date_naissance']); ?></td>
                                <td><?php echo htmlspecialchars($patient['identification_number']); ?></td>
                                <td><?php echo htmlspecialchars($patient['phone_number']); ?></td>
                                <td><?php echo htmlspecialchars($patient['situation_familiale']); ?></td>
                                <td><?php echo htmlspecialchars($patient['type_assurance']); ?></td>
                                <td>
                                    <button class="view-btn" onclick="location.href='view_patient.php?id=<?php echo $patient['patient_id']; ?>'"><img src="Front/imag/view.png" alt="View"></button>
                                    <button class="edit-btn" onclick="location.href='edit_patient.php?id=<?php echo $patient['patient_id']; ?>'"><img src="Front/imag/write.png" alt="Edit"></button>
                                    <button class="delete-btn" onclick="if(confirm('Are you sure you want to delete this patient?')) location.href='delete_patient.php?id=<?php echo $patient['patient_id']; ?>'"><img src="Front/imag/delete.png" alt="Delete"></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
