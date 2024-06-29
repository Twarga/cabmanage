<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Fetch facture data
$facture_data = $facture->readOne($prelevement_id);
if (!$facture_data) {
    die('ERROR: Facture not found.');
}

// Handle form submission for updating a prelevement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_prelevement'])) {
    try {
        // Set prelevement properties
        $prelevement->prelevement_id = $prelevement_id;
        $prelevement->patient_id = $prelevement_data['patient_id'];
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = $_POST['date_creation'];
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->ordonnance = $_FILES['ordonnance']['tmp_name'] ? file_get_contents($_FILES['ordonnance']['tmp_name']) : $prelevement_data['ordonnance'];
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->examen_id = $_POST['examen_id'];

        // Update prelevement
        if ($prelevement->update()) {
            // Set facture properties
            $facture->facture_id = $facture_data['facture_id'];
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

            // Update facture
            if ($facture->update()) {
                echo "Prelevement and Facture updated successfully for prelevement_id " . $prelevement->prelevement_id . ".<br>";
            } else {
                echo "Error updating facture for prelevement_id " . $prelevement->prelevement_id . ".<br>";
            }
        } else {
            echo "Error updating prelevement for prelevement_id " . $prelevement->prelevement_id . ".<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Prelevement</title>
</head>
<body>
    <h2>Update Prelevement for <?php echo htmlspecialchars($prelevement_data['patient_id']); ?></h2>
    <form method="post" enctype="multipart/form-data" action="update_prelevement_assistant.php?id=<?php echo $prelevement_id; ?>">
        <h3>Prelevement Information</h3>
        <label>Type Prelevement:</label>
        <select name="type_prelevement" required>
            <option value="Biopsie" <?php if ($prelevement_data['type_prelevement'] == 'Biopsie') echo 'selected'; ?>>Biopsie</option>
            <option value="Cytologie" <?php if ($prelevement_data['type_prelevement'] == 'Cytologie') echo 'selected'; ?>>Cytologie</option>
            <option value="Pièce opératoire" <?php if ($prelevement_data['type_prelevement'] == 'Pièce opératoire') echo 'selected'; ?>>Pièce opératoire</option>
            <option value="Immuno Histochimique" <?php if ($prelevement_data['type_prelevement'] == 'Immuno Histochimique') echo 'selected'; ?>>Immuno Histochimique</option>
        </select><br>
        <label>Date Reception:</label><input type="date" name="date_reception" value="<?php echo htmlspecialchars($prelevement_data['date_reception']); ?>" required><br>
        <label>Date Creation:</label><input type="date" name="date_creation" value="<?php echo htmlspecialchars($prelevement_data['date_creation']); ?>" required><br>
        <label>Nombre de flacons:</label><input type="number" name="nombre_flacons" value="<?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?>" required><br>
        <label>Ordonnance:</label><input type="file" name="ordonnance"><br>
        <label>Docteur Exterieur:</label><input type="number" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>" required><br>
        <label>Examen:</label>
        <select id="examen_id" name="examen_id" onchange="updateFacture()" required>
            <option value="1" <?php if ($prelevement_data['examen_id'] == 1) echo 'selected'; ?>>Examen Type 1</option>
            <option value="2" <?php if ($prelevement_data['examen_id'] == 2) echo 'selected'; ?>>Examen Type 2</option>
            <option value="3" <?php if ($prelevement_data['examen_id'] == 3) echo 'selected'; ?>>Examen Type 3</option>
        </select><br>
        
        <h3>Facture</h3>
        <label>Total Prix:</label><input type="text" id="total_prix" value="<?php echo htmlspecialchars($facture_data['total_prix']); ?>" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" value="<?php echo htmlspecialchars($facture_data['prix_reduit']); ?>" onchange="updateFacture()" required><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" value="<?php echo htmlspecialchars($facture_data['avance']); ?>" onchange="updateFacture()" required><br>
        <label>Montant Du:</label><input type="text" id="montant_du" value="<?php echo htmlspecialchars($facture_data['montant_du']); ?>" readonly><br>
        <label>Rest:</label><input type="text" id="rest" value="<?php echo htmlspecialchars($facture_data['rest']); ?>" readonly><br>
        
        <button type="submit" name="update_prelevement">Update</button>
        <a href="create_prelevement_assistant.php?patient_id=<?php echo $prelevement_data['patient_id']; ?>"><button type="button">Back to Create Prelevement</button></a>
    </form>
</body>
</html>
