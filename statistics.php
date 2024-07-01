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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statistics</title>
    <style>
        body {
            background-color: #1F4D5A;
            color: #FFF;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: ;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar img {
            max-height: 3rem;
        }
        .navbar a, .navbar span {
            color: #00F8FF;
            text-decoration: none;
            margin-left: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            background-color: #055a6e;
        }
        .navbar a:hover, .navbar span:hover {
            background-color: #044B53;
        }
        .container {
            padding: 1rem;
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
            background-color: #044B53;
        }
        .profile {
            background-color: #088696;
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
    <nav class="navbar">
        <a href="doctor_dashboard.php"><img src="your-logo.png" alt="Logo"></a>
        <div style="flex-grow: 1; display: flex; justify-content: center;">
            <a href="patients.php">Patient</a>
            <a href="prelevement.php">Prélèvement</a>
            <a href="settings.php">Paramétrage</a>
            <a href="doctor_dashboard.php">Tableau de bord</a>
        </div>
        <div style="display: flex; align-items: center;">
            <span class="profile"><img src="profile.png" alt="Profile" style="height: 2rem; width: 2rem; border-radius: 50%;"></span>
            <a href="logout.php">Logout</a>
        </div>
    </nav>
    <section class="section">
        <div class="container">
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
    </section>
</body>
</html>
