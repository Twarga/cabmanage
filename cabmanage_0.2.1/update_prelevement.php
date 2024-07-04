<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Template.php';
require_once 'Patient.php'; // Include the Patient class
require_once 'DocteurExterieur.php'; // Include the DocteurExterieur class

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);
$patient = new Patient($db);
$docteurExterieur = new DocteurExterieur($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
$facture_data = $facture->readOne($prelevement_id);
$template_data = $template->readOne($prelevement_data['rapport_template']);
$patient_data = $patient->readOne($prelevement_data['patient_id']);
$doctor_data = $docteurExterieur->readOne($prelevement_data['docteur_exterieur_id']);

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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 10px 0;
        }
        .header div {
            width: 48%;
        }
        .header div:first-child {
            border-right: 1px solid #000;
            padding-right: 10px;
        }
        .header div:last-child {
            padding-left: 10px;
        }
        .section {
            margin-top: 20px;
        }
        .section-title {
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 50px;
            margin-top: 20px;
        }
        .section-content {
            margin-top: 10px;
        }
        .table-container {
            display: flex;
            justify-content: space-between;
        }
        .table-container div {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 20px;
        }
        .footer div {
            width: 48%;
        }
        .footer div:first-child {
            border-right: 1px solid #000;
            padding-right: 10px;
        }
        .footer div:last-child {
            padding-left: 10px;
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
        <div class="container">
            <div class="title">Laboratoire d'anatomie et cytologie pathologique AL AMAL</div>
            <div class="header">
                <div>
                    <strong>Reçu à rapporter lors du retrait des résultats</strong><br>
                    <br>
                    Nom et Prénom: <?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom']); ?><br>
                    Code Patient: <?php echo htmlspecialchars($patient_data['code'] ?? 'N/A'); ?><br>
                    Date: <?php echo htmlspecialchars($prelevement_data['date_creation']); ?><br>
                    Référence: <?php echo htmlspecialchars($prelevement_data['prelevement_id']); ?><br>
                    Médecin: <?php echo htmlspecialchars($doctor_data['full_name']); ?><br>
                    Net à payer: <?php echo htmlspecialchars($facture_data['total_prix'] . ' DH'); ?><br>
                    Avance: <?php echo htmlspecialchars($facture_data['avance'] . ' DH'); ?><br>
                    Solde: <?php echo htmlspecialchars($facture_data['rest'] . ' DH'); ?><br>
                    <div class="barcode">
                        <img src="barcode-placeholder.png" alt="Barcode">
                    </div>
                </div>
                <div>
                    <strong>CARTE DE DOSSIER</strong><br>
                    <br>
                    Nom et Prénom: <?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom']); ?><br>
                    Code: <?php echo htmlspecialchars($patient_data['code'] ?? 'N/A'); ?><br>
                    <div class="barcode">
                        <img src="barcode-placeholder.png" alt="Barcode">
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="section-title">Facture</div>
                <div class="section-content">
                    <table>
                        <tr>
                            <th>Net à payer</th>
                            <th>Avance</th>
                            <th>Solde</th>
                        </tr>
                        <tr>
                            <td><?php echo htmlspecialchars($facture_data['total_prix'] . ' DH'); ?></td>
                            <td><?php echo htmlspecialchars($facture_data['avance'] . ' DH'); ?></td>
                            <td><?php echo htmlspecialchars($facture_data['rest'] . ' DH'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="section">
                <div class="section-title">Ordonnances</div>
                <div class="table-container">
                    <div>
                        <table>
                            <tr>
                                <th>Réception</th>
                                <td><?php echo htmlspecialchars($prelevement_data['date_reception']); ?></td>
                            </tr>
                            <tr>
                                <th>Référence</th>
                                <td><?php echo htmlspecialchars($prelevement_data['prelevement_id']); ?></td>
                            </tr>
                            <tr>
                                <th>Créé par</th>
                                <td><?php echo htmlspecialchars($prelevement_data['created_by'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Code Patient</th>
                                <td><?php echo htmlspecialchars($patient_data['code'] ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div>
                        <table>
                            <tr>
                                <th>Date</th>
                                <td><?php echo htmlspecialchars($prelevement_data['date_creation']); ?></td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td><?php echo htmlspecialchars($patient_data['age'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Tél</th>
                                <td><?php echo htmlspecialchars($patient_data['phone'] ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div>
                    <strong>Prélevements</strong>: <?php echo htmlspecialchars($prelevement_data['type_prelevement']); ?><br>
                    <strong>Nombre de Flacons/lames</strong>: <?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?><br>
                    <strong>Compléments</strong>: <?php echo htmlspecialchars($prelevement_data['complements'] ?? 'N/A'); ?><br>
                    <strong>Historique</strong>: <?php echo htmlspecialchars($prelevement_data['history'] ?? 'N/A'); ?>
                </div>
                <div>
                    <div class="barcode">
                        <img src="barcode-placeholder.png" alt="Barcode">
                    </div>
                </div>
            </div>
        </div>
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
