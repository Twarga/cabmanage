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

// Fetch all patients
$patients = $patient->read();
if (!$patients) {
    echo "No patients found.<br>";
}

// Fetch all factures for today
$date_today = date('Y-m-d');
$factures_today_query = "SELECT * FROM factures WHERE DATE(date_creation) = '$date_today' OR DATE(date_modification) = '$date_today'";
$factures_today_result = $db->query($factures_today_query);

$totalMoneyToday = 0;
$totalUnpaidToday = 0;

if ($factures_today_result) {
    while ($facture_data = $factures_today_result->fetch_assoc()) {
        $totalMoneyToday += $facture_data['prix_reduit'] + $facture_data['avance'];
        $totalUnpaidToday += $facture_data['rest'];
    }
} else {
    echo "Error fetching today's factures: " . $db->error;
}

// Count total patients
$totalPatients = count($patients);

// Count total prelevements
$totalPrelevements = $prelevement->countAll();

// Count facture/prelevement status
$fullyPaidCount = $facture->countByStatus('Payé');
$partiallyPaidCount = $facture->countByStatus('Partiellement payé');
$notPaidCount = $facture->countByStatus('Non payé');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics</title>
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

        .stat-box {
            background-color: #088696;
            color: #FFF;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1rem;
            flex: 1;
            margin: 0.5rem;
        }
        .stat-box .title {
            color: #00F8FF;
            font-size: 2rem;
        }
        .stat-box .subtitle {
            color: #FFF;
            font-size: 1rem;
        }
        .columns {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .table-container {
            margin-top: 2rem;
            overflow-x: auto;
        }
        .table {
            width: 100%;
            background-color: #088696;
            color: #FFF;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #00F8FF;
            
            padding: 0.5rem;
            text-align: left;
        }
        .table th {
            background-color: #00E6FF;
            color: #000;
        }
        .navbar .profile {
            background-color: transparent;
            border-radius: 50%;
            padding: 1rem;
        }

        @media (max-width: 768px) {
            .columns {
                flex-direction: column;
            }
            .stat-box {
                margin: 0.5rem 0;
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
                <button class="nav-button" onclick="location.href='statistics_assistance.php'">Tableau de bord</button>
                <button class="nav-button" onclick="location.href='patient_management_assistance.php'">Patient</button>
                <div class="user-section">
                    <img src="Front/imag/inf.jpeg" alt="User Icon" class="user-icon">
                </div>
                <button class="nav-button" onclick="location.href='prelevement_management_assistant.php'">Prélèvement</button>
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
            <h1>Statistiques</h1>
            <div class="columns">
                <div class="stat-box">
                    <p class="title"><?php echo $totalPatients; ?></p>
                    <p class="subtitle">Total Patient</p>
                </div>
                <div class="stat-box">
                    <p class="title"><?php echo $totalPrelevements; ?></p>
                    <p class="subtitle">Total Prélèvement</p>
                </div>
                <div class="stat-box">
                    <p class="title"><?php echo $totalMoneyToday; ?> MD</p>
                    <p class="subtitle">Total Money Today</p>
                </div>
                <div class="stat-box">
                    <p class="title"><?php echo $totalUnpaidToday; ?> MAD</p>
                    <p class="subtitle">Total Unpaid Today</p>
                </div>
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>N° of Prélèvement</th>
                            <th>Total Paid Amount</th>
                            <th>Unpaid Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($patients as $patient) {
                            if (!isset($patient['patient_id'])) {
                                echo "<tr><td colspan='6'>Patient ID not set.</td></tr>";
                                continue;
                            }
                            $prelevements = $prelevement->readByPatient($patient['patient_id']);
                            $totalPaidAmount = 0;
                            $unpaidAmount = 0;
                            foreach ($prelevements as $prelev) {
                                $facture_data = $facture->readOne($prelev['prelevement_id']);
                                if ($facture_data) {
                                    $totalPaidAmount += $facture_data['prix_reduit'] + $facture_data['avance'];
                                    $unpaidAmount += $facture_data['rest'];
                                } else {
                                    echo "<tr><td colspan='6'>No facture data for prelevement ID: " . $prelev['prelevement_id'] . "</td></tr>";
                                }
                            }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['prenom']); ?></td>
                                <td><?php echo count($prelevements); ?></td>
                                <td><?php echo $totalPaidAmount; ?> MAD</td>
                                <td><?php echo $unpaidAmount; ?> MAD</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
