<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Template.php';

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);

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
        $prelevement->rapport_template = $_POST['rapport_template'];
        $prelevement->rapport_txt = $_POST['rapport_txt'];
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

// Fetch all templates
$templates = $template->readAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Prelevement</title>
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadTemplate() {
            const templateId = $('#rapport_template').val();
            if (templateId) {
                $.get('load_template.php', { template_id: templateId }, function(data) {
                    const template = JSON.parse(data);
                    CKEDITOR.instances.rapport_txt.setData(template.content);
                });
            }
        }

        function updateFacture() {
            const examenId = $('#examen_id').val();
            const prixReduit = parseFloat($('#prix_reduit').val()) || 0;
            const avance = parseFloat($('#avance').val()) || 0;
            const totalPrix = 100.0 * examenId; // Example calculation
            const montantDu = totalPrix - prixReduit - avance;
            const rest = montantDu;

            $('#total_prix').val(totalPrix);
            $('#montant_du').val(montantDu);
            $('#rest').val(rest);
        }

        function confirmDelete(prelevement_id) {
            if (confirm('Are you sure you want to delete this prelevement?')) {
                window.location.href = 'delete_prelevement.php?id=' + prelevement_id;
            }
        }

        function saveTemplate() {
            const templateName = prompt('Enter template name:');
            if (templateName) {
                const rapportContent = CKEDITOR.instances.rapport_txt.getData();
                $.post('create_prelevement.php?patient_id=<?php echo $prelevement_data['patient_id']; ?>', {
                    save_template: true,
                    template_name: templateName,
                    rapport_txt: rapportContent
                }, function(data) {
                    alert(data);
                    location.reload();
                });
            }
        }
    </script>
</head>
<body>
    <h2>Update Prelevement for <?php echo htmlspecialchars($prelevement_data['patient_id']); ?></h2>
    <form method="post" enctype="multipart/form-data" action="update_prelevement.php?id=<?php echo $prelevement_id; ?>">
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
        
        <h3>Rapport</h3>
        <label>Rapport Template:</label>
        <input type="text" id="rapport_template_search" placeholder="Search Template">
        <select id="rapport_template" name="rapport_template" onchange="loadTemplate()">
            <option value="">Select Template</option>
            <?php foreach ($templates as $template): ?>
                <option value="<?php echo htmlspecialchars($template['template_id']); ?>" <?php if ($prelevement_data['rapport_template'] == $template['template_id']) echo 'selected'; ?>><?php echo htmlspecialchars($template['name']); ?></option>
            <?php endforeach; ?>
        </select><br>
        <textarea name="rapport_txt" id="rapport_txt"><?php echo htmlspecialchars($prelevement_data['rapport_txt']); ?></textarea>
        <script>
            CKEDITOR.replace('rapport_txt');
        </script>
        <br>
        <button type="submit" name="update_prelevement">Update</button>
    </form>
</body>
</html>
