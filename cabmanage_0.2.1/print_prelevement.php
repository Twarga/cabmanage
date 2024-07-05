<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Template.php';
require_once 'Patient.php';
require_once 'DocteurExterieur.php'; // Ensure this file exists and is correctly named
require_once 'Examen.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);
$patient = new Patient($db);
$docteur = new DocteurExterieur($db);
$examen = new Examen($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
$facture_data = $facture->readOne($prelevement_id);
$template_data = $template->readOne($prelevement_data['rapport_template']);

if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Fetch patient data using patient_id from prelevement_data
$patient_data = $patient->readOne($prelevement_data['patient_id']);
if (!$patient_data) {
    die('ERROR: Patient not found.');
}

// Fetch user data for the created_by field
$created_by_user = 'N/A';
if (isset($prelevement_data['created_by'])) {
    $created_by_user_data = $db->query("SELECT name, prenom FROM users WHERE user_id = " . intval($prelevement_data['created_by']))->fetch_assoc();
    if ($created_by_user_data) {
        $created_by_user = $created_by_user_data['name'] . " " . $created_by_user_data['prenom'];
    }
}

// Fetch doctor data
$doctor_name = 'N/A';
if (isset($prelevement_data['docteur_exterieur_id'])) {
    $doctor_data = $docteur->readOne($prelevement_data['docteur_exterieur_id']);
    if ($doctor_data) {
        $doctor_name = $doctor_data['full_name'];
    }
}

// Fetch exams data
$examens_list = $examen->readAllByPrelevementNumber($prelevement_data['prelevement_id']);

// Check if the facture is fully paid
$canPrintRapport = isset($facture_data['etat_paiement']) && ($facture_data['etat_paiement'] == 'Payé');

// Function to get the value from the array or return 'N/A'
function getValue($array, $key) {
    return isset($array[$key]) ? htmlspecialchars($array[$key]) : 'N/A';
}

$patient_name = urlencode(getValue($patient_data, 'name') . ' ' . getValue($patient_data, 'prenom'));
$patient_code = urlencode(getValue($prelevement_data, 'patient_id'));
$date = urlencode(getValue($prelevement_data, 'date_reception'));
$reference = urlencode(getValue($prelevement_data, 'prelevement_id'));
$doctor_name = urlencode($doctor_name);
$total_price = urlencode(getValue($facture_data, 'total_prix'));
$advance = urlencode(getValue($facture_data, 'avance'));
$balance = urlencode(getValue($facture_data, 'rest'));
$created_by = urlencode($created_by_user);
$age = urlencode(getValue($patient_data, 'age'));
$telephone = urlencode(getValue($patient_data, 'phone_number'));
$prelevements = urlencode(getValue($prelevement_data, 'type_prelevement'));
$num_flacons = urlencode(getValue($prelevement_data, 'nombre_flacons'));
$complements = urlencode(getValue($prelevement_data, 'compléments'));
$history = urlencode(getValue($patient_data, 'prelevement_history'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Prelevement</title>
    <script>
        function printSection(url) {
            const printWindow = window.open(url, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }
    </script>
</head>
<body>
    <h2>Prelevement Details</h2>
    <button onclick="printSection('prelevement_template.php?patient_name=<?php echo $patient_name; ?>&patient_code=<?php echo $patient_code; ?>&date=<?php echo $date; ?>&reference=<?php echo $reference; ?>&doctor_name=<?php echo $doctor_name; ?>&total_price=<?php echo $total_price; ?>&advance=<?php echo $advance; ?>&balance=<?php echo $balance; ?>&created_by=<?php echo $created_by; ?>&age=<?php echo $age; ?>&telephone=<?php echo $telephone; ?>&prelevements=<?php echo $prelevements; ?>&num_flacons=<?php echo $num_flacons; ?>&complements=<?php echo $complements; ?>&history=<?php echo $history; ?>')">Print Prelevement Information</button>
    <button onclick="printSection('facture_template.php?prelevement_id=<?php echo $prelevement_id; ?>&patient_name=<?php echo $patient_name; ?>&demande_number=<?php echo $reference; ?>&date_demande=<?php echo $date; ?>&doctor_name=<?php echo $doctor_name; ?>&date_facturation=<?php echo urlencode($facture_data['date_creation']); ?>&facture_id=<?php echo urlencode($facture_data['facture_id']); ?>&total_price=<?php echo $total_price; ?>&mode_reglement=Especé')">Print Facture</button>
    <?php if ($canPrintRapport): ?>
        <button onclick="printSection('rapport_template.php?id=<?php echo $prelevement_id; ?>')">Print Rapport</button>
    <?php else: ?>
        <p>The rapport can only be printed if the facture is fully paid.</p>
    <?php endif; ?>
</body>
</html>
