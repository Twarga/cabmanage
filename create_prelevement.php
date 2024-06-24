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

// Get the patient ID from the URL
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : die('ERROR: Patient ID not found.');

// Fetch patient data
$patient_data = $patient->readOne($patient_id);

// Handle form submission for creating a prelevement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Set prelevement properties
        $prelevement->patient_id = $patient_id;
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = date('Y-m-d');
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->ordonnance = $_FILES['ordonnance']['tmp_name'] ? file_get_contents($_FILES['ordonnance']['tmp_name']) : null;
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->rapport_template = $_POST['rapport_template'];
        $prelevement->rapport_txt = $_POST['rapport_txt'];
        $prelevement->examen_id = $_POST['examen_id'];

        // Create prelevement
        if ($prelevement->create()) {
            // Set facture properties
            $facture->examen_id = $prelevement->examen_id;
            $facture->prelevement_id = $prelevement->prelevement_id;
            $facture->total_prix = 100.0 * $prelevement->examen_id; // Example calculation
            $facture->prix_reduit = $_POST['prix_reduit'];
            $facture->avance = $_POST['avance'];
            $facture->montant_du = $facture->total_prix - $facture->prix_reduit - $facture->avance;
            $facture->rest = $facture->montant_du;
            
            if ($facture->montant_du == 0) {
                $facture->etat_paiement = 'Payé';
            } elseif ($facture->avance > 0) {
                $facture->etat_paiement = 'Partiellement payé';
            } else {
                $facture->etat_paiement = 'Non payé';
            }

            // Create facture
            if ($facture->create()) {
                echo "Prelevement and Facture created successfully for patient_id " . $prelevement->patient_id . ".<br>";
            } else {
                echo "Error creating facture for prelevement_id " . $prelevement->prelevement_id . ".<br>";
            }
        } else {
            echo "Error creating prelevement for patient_id " . $prelevement->patient_id . ".<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch prelevement history for the patient
$prelevements_history = $prelevement->readByPatient($patient_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Prelevement</title>
    <script>
        function updateFacture() {
            const examenId = document.getElementById('examen_id').value;
            let totalPrix = 0;
            switch (examenId) {
                case '1':
                    totalPrix = 100;
                    break;
                case '2':
                    totalPrix = 200;
                    break;
                case '3':
                    totalPrix = 300;
                    break;
                default:
                    totalPrix = 0;
            }

            const prixReduit = parseFloat(document.getElementById('prix_reduit').value) || 0;
            const avance = parseFloat(document.getElementById('avance').value) || 0;
            const montantDu = totalPrix - prixReduit - avance;
            const rest = montantDu;

            document.getElementById('total_prix').value = totalPrix;
            document.getElementById('montant_du').value = montantDu;
            document.getElementById('rest').value = rest;
        }
    </script>
</head>
<body>
    <h2>Create Prelevement for <?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom']); ?></h2>
    <form method="post" enctype="multipart/form-data" action="create_prelevement.php?patient_id=<?php echo $patient_id; ?>">
        <label>Type Prelevement:</label>
        <select name="type_prelevement" required>
            <option value="Biopsie">Biopsie</option>
            <option value="Cytologie">Cytologie</option>
            <option value="Pièce opératoire">Pièce opératoire</option>
            <option value="Immuno Histochimique">Immuno Histochimique</option>
        </select><br>
        <label>Date Reception:</label><input type="date" name="date_reception" required><br>
        <label>Nombre de flacons:</label><input type="number" name="nombre_flacons" required><br>
        <label>Ordonnance:</label><input type="file" name="ordonnance"><br>
        <label>Docteur Exterieur:</label><input type="number" name="docteur_exterieur_id" required><br>
        <label>Rapport Template:</label><input type="text" name="rapport_template"><br>
        <label>Rapport Text:</label><input type="text" name="rapport_txt"><br>
        <label>Examen:</label>
        <select id="examen_id" name="examen_id" onchange="updateFacture()" required>
            <option value="1">Examen Type 1</option>
            <option value="2">Examen Type 2</option>
            <option value="3">Examen Type 3</option>
        </select><br>

        <h2>Facture</h2>
        <label>Total Prix:</label><input type="text" id="total_prix" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" onchange="updateFacture()" required><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" onchange="updateFacture()" required><br>
        <label>Montant Du:</label><input type="text" id="montant_du" readonly><br>
        <label>Rest:</label><input type="text" id="rest" readonly><br>
        
        <button type="submit">Create</button>
    </form>

    <h2>Prelevement History</h2>
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
            <th>Edit</th>
            <th>Delete</th>
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
                <td><a href="edit_prelevement.php?id=<?php echo $history['prelevement_id']; ?>">Edit</a></td>
                <td><a href="delete_prelevement.php?id=<?php echo $history['prelevement_id']; ?>">Delete</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
