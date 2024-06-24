<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';

// Initialize the Prelevement class
$db = $link;
$prelevement = new Prelevement($db);

// Fetch prelevement data for editing
if (isset($_GET['id'])) {
    $prelevement_id = $_GET['id'];
    $prelevement_data = $prelevement->readOne($prelevement_id);
}

// Handle form submission for updating prelevement data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $prelevement->prelevement_id = $_POST['prelevement_id'];
        $prelevement->patient_id = $_POST['patient_id'];
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = $_POST['date_creation'];
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->ordonnance = null; // Handle file upload if necessary
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->rapport_template = $_POST['rapport_template'];
        $prelevement->rapport_txt = $_POST['rapport_txt'];
        $prelevement->examen_id = $_POST['examen_id'];
        $prelevement->facture_id = $_POST['facture_id'];

        if ($prelevement->update()) {
            header("Location: create_prelevement.php?patient_id=" . $_POST['patient_id']);
            exit;
        } else {
            echo "Error updating prelevement.";
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
    <title>Edit Prelevement</title>
</head>
<body>
    <h2>Edit Prelevement</h2>
    <form method="post" action="edit_prelevement.php">
        <input type="hidden" name="prelevement_id" value="<?php echo htmlspecialchars($prelevement_data['prelevement_id']); ?>">
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($prelevement_data['patient_id']); ?>">
        <label>Type Prelevement:</label>
        <select name="type_prelevement" required>
            <option value="Biopsie" <?php if($prelevement_data['type_prelevement'] == 'Biopsie') echo 'selected'; ?>>Biopsie</option>
            <option value="Cytologie" <?php if($prelevement_data['type_prelevement'] == 'Cytologie') echo 'selected'; ?>>Cytologie</option>
            <option value="Pièce opératoire" <?php if($prelevement_data['type_prelevement'] == 'Pièce opératoire') echo 'selected'; ?>>Pièce opératoire</option>
            <option value="Immuno Histochimique" <?php if($prelevement_data['type_prelevement'] == 'Immuno Histochimique') echo 'selected'; ?>>Immuno Histochimique</option>
        </select><br>
        <label>Date Reception:</label><input type="date" name="date_reception" value="<?php echo htmlspecialchars($prelevement_data['date_reception']); ?>" required><br>
        <label>Date Creation:</label><input type="date" name="date_creation" value="<?php echo htmlspecialchars($prelevement_data['date_creation']); ?>" required><br>
        <label>Nombre de Flacons:</label><input type="number" name="nombre_flacons" value="<?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?>" required><br>
        <label>Docteur Exterieur:</label><input type="number" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>" required><br>
        <label>Rapport Template:</label><input type="text" name="rapport_template" value="<?php echo htmlspecialchars($prelevement_data['rapport_template']); ?>"><br>
        <label>Rapport Text:</label><input type="text" name="rapport_txt" value="<?php echo htmlspecialchars($prelevement_data['rapport_txt']); ?>"><br>
        <label>Examen:</label><input type="number" name="examen_id" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>" required><br>
        <label>Facture ID:</label><input type="number" name="facture_id" value="<?php echo htmlspecialchars($prelevement_data['facture_id']); ?>" required><br>
        <button type="submit">Update</button>
    </form>

    <a href="create_prelevement.php?patient_id=<?php echo htmlspecialchars($prelevement_data['patient_id']); ?>">Back to Prelevement Management</a>
</body>
</html>
