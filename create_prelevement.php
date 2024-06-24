<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Patient.php';
require_once 'Examen.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$patient = new Patient($db);
$examen = new Examen($db);
$facture = new Facture($db);

// Fetch patient data and prelevement history if a patient is selected
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;
$patient_data = $patient_id ? $patient->readOne($patient_id) : null;
$prelevement_history = $patient_id ? $prelevement->readByPatient($patient_id) : [];

// Fetch examens for the dropdown
$examens = $examen->read();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $prelevement->patient_id = $_POST['patient_id'];
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = date('Y-m-d'); // Current date
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->ordonnance = null; // Handle file upload if necessary
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->rapport_template = $_POST['rapport_template'];
        $prelevement->rapport_txt = $_POST['rapport_txt'];
        $prelevement->examen_id = $_POST['examen_id'];

        // Facture details
        $facture->examen_id = $prelevement->examen_id;
        $facture->total_prix = $_POST['total_prix'];
        $facture->prix_reduit = $_POST['prix_reduit'];
        $facture->avance = $_POST['avance'];
        $facture->montant_du = $facture->total_prix - $facture->prix_reduit - $facture->avance;
        $facture->rest = $facture->montant_du;
        $facture->etat_paiement = $_POST['etat_paiement'];

        // Create prelevement and facture
        if ($prelevement->create()) {
            $facture->prelevement_id = $prelevement->prelevement_id; // This needs to be fetched
            if ($facture->create()) {
                header("Location: create_prelevement.php?patient_id=" . $_POST['patient_id']);
                exit;
            } else {
                echo "Error creating facture.";
            }
        } else {
            echo "Error creating prelevement.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle deletion of a prelevement
if (isset($_GET['delete_id'])) {
    $prelevement_id = $_GET['delete_id'];
    if ($prelevement->delete($prelevement_id)) {
        header("Location: create_prelevement.php?patient_id=" . $patient_id);
        exit;
    } else {
        echo "Error deleting prelevement.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Prelevement</title>
    <script>
        const examens = <?php echo json_encode($examens); ?>;

        function updateFacture() {
            const examenSelect = document.getElementById('examen_id');
            const selectedExamen = examens.find(examen => examen.examen_id == examenSelect.value);
            if (selectedExamen) {
                document.getElementById('total_prix').value = selectedExamen.prix;
                calculateFacture();
            }
        }

        function calculateFacture() {
            const totalPrix = parseFloat(document.getElementById('total_prix').value);
            const prixReduit = parseFloat(document.getElementById('prix_reduit').value);
            const avance = parseFloat(document.getElementById('avance').value);
            const montantDu = totalPrix - prixReduit - avance;
            const rest = montantDu;

            document.getElementById('montant_du').value = montantDu.toFixed(2);
            document.getElementById('rest').value = rest.toFixed(2);
        }
    </script>
</head>
<body>
    <h2>Create Prelevement</h2>

    <h3>New Prelevement</h3>
    <form method="post" action="create_prelevement.php">
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patient_id); ?>">
        <label>Type Prelevement:</label>
        <select name="type_prelevement" required>
            <option value="Biopsie">Biopsie</option>
            <option value="Cytologie">Cytologie</option>
            <option value="Pièce opératoire">Pièce opératoire</option>
            <option value="Immuno Histochimique">Immuno Histochimique</option>
        </select><br>
        <label>Date Reception:</label><input type="date" name="date_reception" required><br>
        <label>Nombre de Flacons:</label><input type="number" name="nombre_flacons" required><br>
        <label>Docteur Exterieur:</label><input type="number" name="docteur_exterieur_id" required><br>
        <label>Rapport Template:</label><input type="text" name="rapport_template"><br>
        <label>Rapport Text:</label><input type="text" name="rapport_txt"><br>
        <label>Examen:</label>
        <select name="examen_id" id="examen_id" onchange="updateFacture()" required>
            <?php foreach ($examens as $examen): ?>
                <option value="<?php echo $examen['examen_id']; ?>"><?php echo htmlspecialchars($examen['sub_type']); ?></option>
            <?php endforeach; ?>
        </select><br>

        <h3>Facture</h3>
        <label>Total Prix:</label><input type="number" id="total_prix" name="total_prix" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" value="0" oninput="calculateFacture()"><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" value="0" oninput="calculateFacture()"><br>
        <label>Montant Du:</label><input type="number" id="montant_du" name="montant_du" readonly><br>
        <label>Rest:</label><input type="number" id="rest" name="rest" readonly><br>
        <label>Etat Paiement:</label>
        <select name="etat_paiement" required>
            <option value="Non payé">Non payé</option>
            <option value="Partiellement payé">Partiellement payé</option>
            <option value="Payé">Payé</option>
        </select><br>

        <button type="submit">Create</button>
    </form>

    <?php if ($prelevement_history): ?>
        <h3>Prelevement History</h3>
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
            <?php foreach ($prelevement_history as $history): ?>
                <tr>
                    <td><?php echo htmlspecialchars($history['prelevement_id']); ?></td>
                    <td><?php echo htmlspecialchars($history['type_prelevement']); ?></td>
                    <td><?php echo htmlspecialchars($history['date_reception']); ?></td>
                    <td><?php echo htmlspecialchars($history['date_creation']); ?></td>
                    <td><?php echo htmlspecialchars($history['nombre_flacons']); ?></td>
                    <td><?php echo htmlspecialchars($history['docteur_exterieur_id']); ?></td>
                    <td><?php echo htmlspecialchars($history['etat_paiement']); ?></td>
                    <td><?php echo htmlspecialchars($history['rest']); ?></td>
                    <td><a href="edit_prelevement.php?id=<?php echo $history['prelevement_id']; ?>">Edit</a></td>
                    <td><a href="create_prelevement.php?patient_id=<?php echo $patient_id; ?>&delete_id=<?php echo $history['prelevement_id']; ?>" onclick="return confirm('Are you sure you want to delete this prelevement?');">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <a href="patient_management.php">Back to Patient Management</a>
</body>
</html>
