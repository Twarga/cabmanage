<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Template.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
$facture_data = $facture->readOne($prelevement_id);
$template_data = $template->readOne($prelevement_data['rapport_template']);

if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Check if the facture is fully paid
$canPrintRapport = isset($facture_data['etat_paiement']) && ($facture_data['etat_paiement'] == 'Payé');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Prelevement</title>
    <style>
        .print-section { display: none; }
        .bill {
            border: 1px solid #000;
            padding: 10px;
            width: 300px;
            font-family: Arial, sans-serif;
        }
        .bill-header, .bill-footer {
            text-align: center;
        }
        .bill-body {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
    <script>
        function printSection(sectionId) {
            const section = document.getElementById(sectionId).innerHTML;
            const originalContent = document.body.innerHTML;
            document.body.innerHTML = section;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
</head>
<body>
    <h2>Prelevement Details</h2>
    <div id="prelevement-info" class="print-section">
        <table border="1">
            <tr><td>Prelevement ID</td><td><?php echo htmlspecialchars($prelevement_data['prelevement_id']); ?></td></tr>
            <tr><td>Type</td><td><?php echo htmlspecialchars($prelevement_data['type_prelevement']); ?></td></tr>
            <tr><td>Date Reception</td><td><?php echo htmlspecialchars($prelevement_data['date_reception']); ?></td></tr>
            <tr><td>Date Creation</td><td><?php echo htmlspecialchars($prelevement_data['date_creation']); ?></td></tr>
            <tr><td>Nombre de Flacons</td><td><?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?></td></tr>
            <tr><td>Docteur Exterieur</td><td><?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?></td></tr>
        </table>
    </div>
    <div id="facture-info" class="print-section">
        <div class="bill">
            <div class="bill-header">
                <h3>Facture</h3>
            </div>
            <div class="bill-body">
                <div class="bill-row"><span>Total Prix:</span><span><?php echo htmlspecialchars($facture_data['total_prix'] ?? 'N/A'); ?></span></div>
                <div class="bill-row"><span>Prix Reduit:</span><span><?php echo htmlspecialchars($facture_data['prix_reduit'] ?? 'N/A'); ?></span></div>
                <div class="bill-row"><span>Avance:</span><span><?php echo htmlspecialchars($facture_data['avance'] ?? 'N/A'); ?></span></div>
                <div class="bill-row"><span>Montant Du:</span><span><?php echo htmlspecialchars($facture_data['montant_du'] ?? 'N/A'); ?></span></div>
                <div class="bill-row"><span>Rest:</span><span><?php echo htmlspecialchars($facture_data['rest'] ?? 'N/A'); ?></span></div>
                <div class="bill-row"><span>Etat Paiement:</span><span><?php echo htmlspecialchars($facture_data['etat_paiement'] ?? 'N/A'); ?></span></div>
            </div>
            <div class="bill-footer">
                <p>Merci pour votre visite!</p>
            </div>
        </div>
    </div>
    <div id="rapport-info" class="print-section">
        <h3>Rapport</h3>
        <p><?php echo htmlspecialchars(strip_tags($template_data['content'] ?? '')); ?></p>
        <p><?php echo htmlspecialchars(strip_tags($prelevement_data['rapport_txt'] ?? '')); ?></p>
    </div>
    <button onclick="printSection('prelevement-info')">Print Prelevement Information</button>
    <button onclick="printSection('facture-info')">Print Facture</button>
    <?php if ($canPrintRapport): ?>
        <button onclick="printSection('rapport-info')">Print Rapport</button>
    <?php else: ?>
        <p>The rapport can only be printed if the facture is fully paid.</p>
    <?php endif; ?>
</body>
</html>
