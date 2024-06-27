<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Template.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$template = new Template($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Fetch all templates
$templates = $template->readAll();

// Handle form submission for updating a prelevement
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Set prelevement properties
        $prelevement->prelevement_id = $prelevement_id;
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = $_POST['date_creation'];
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->rapport_template = $_POST['rapport_template'];
        $prelevement->rapport_txt = $_POST['rapport_txt'];
        $prelevement->examen_id = $_POST['examen_id'];
        $prelevement->facture_id = $_POST['facture_id'];

        // Update prelevement
        if ($prelevement->update()) {
            echo "Prelevement updated successfully.";
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

        // Search functionality for templates
        $(document).ready(function() {
            $('#rapport_template_search').on('input', function() {
                const searchQuery = $(this).val().toLowerCase();
                $('#rapport_template option').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchQuery)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <h2>Edit Prelevement</h2>
    <form method="post" action="edit_prelevement.php?id=<?php echo $prelevement_id; ?>">
        <label>Type Prelevement:</label>
        <select name="type_prelevement" required>
            <option value="Biopsie" <?php echo $prelevement_data['type_prelevement'] == 'Biopsie' ? 'selected' : ''; ?>>Biopsie</option>
            <option value="Cytologie" <?php echo $prelevement_data['type_prelevement'] == 'Cytologie' ? 'selected' : ''; ?>>Cytologie</option>
            <option value="Pièce opératoire" <?php echo $prelevement_data['type_prelevement'] == 'Pièce opératoire' ? 'selected' : ''; ?>>Pièce opératoire</option>
            <option value="Immuno Histochimique" <?php echo $prelevement_data['type_prelevement'] == 'Immuno Histochimique' ? 'selected' : ''; ?>>Immuno Histochimique</option>
        </select><br>
        <label>Date Reception:</label><input type="date" name="date_reception" value="<?php echo htmlspecialchars($prelevement_data['date_reception']); ?>" required><br>
        <label>Date Creation:</label><input type="date" name="date_creation" value="<?php echo htmlspecialchars($prelevement_data['date_creation']); ?>" required><br>
        <label>Nombre de Flacons:</label><input type="number" name="nombre_flacons" value="<?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?>" required><br>
        <label>Docteur Exterieur:</label><input type="number" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>" required><br>
        <label>Rapport Template:</label>
        <input type="text" id="rapport_template_search" placeholder="Search Template">
        <select id="rapport_template" name="rapport_template" onchange="loadTemplate()">
            <option value="">Select Template</option>
            <?php foreach ($templates as $template): ?>
                <option value="<?php echo htmlspecialchars($template['template_id']); ?>" <?php echo $prelevement_data['rapport_template'] == $template['template_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($template['name']); ?></option>
            <?php endforeach; ?>
        </select><br>
        <textarea name="rapport_txt" id="rapport_txt"><?php echo htmlspecialchars($prelevement_data['rapport_txt']); ?></textarea>
        <script>
            CKEDITOR.replace('rapport_txt');

            // Search functionality for templates
            $('#rapport_template_search').on('input', function() {
                const searchQuery = $(this).val().toLowerCase();
                $('#rapport_template option').each(function() {
                    const text = $(this).text().toLowerCase();
                    if (text.includes(searchQuery)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        </script>
        <br>
        <label>Examen:</label><input type="number" name="examen_id" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>" required><br>
        <label>Facture ID:</label><input type="number" name="facture_id" value="<?php echo htmlspecialchars($prelevement_data['facture_id']); ?>" required><br>
        <button type="submit">Update</button>
    </form>
    <a href="prelevement_management.php">Back to Prelevement Management</a>
</body>
</html>
