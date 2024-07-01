<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Examen.php'; // Include the Examen class

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$examen = new Examen($db); // Initialize the Examen class

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
if (!$prelevement_data) {
    die('ERROR: Prelevement ID not found.');
}

// Fetch facture data for the prelevement
$facture_data = $facture->readOne($prelevement_id);

// Fetch all examens
$examens = $examen->read(); // Fetch all examens
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Prelevement</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .dropdown-search {
            position: relative;
            display: inline-block;
        }

        .dropdown-search input[type="text"] {
            width: 100%;
            box-sizing: border-box;
        }

        .dropdown-search-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 100%;
            overflow: auto;
            border: 1px solid #ddd;
            z-index: 1;
        }

        .dropdown-search-content a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-search a:hover {
            background-color: #ddd;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#search_examen').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.examen a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_examen').on('focus', function () {
                $('.dropdown-search-content.examen').show();
            });

            $('#search_examen').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.examen').hide();
                }, 200);
            });

            $('.dropdown-search-content.examen a').on('click', function () {
                $('#search_examen').val($(this).text());
                $('#examen_id').val($(this).data('id'));
                $('.dropdown-search-content.examen').hide();
                updateFacture();
            });
        });

        function updateFacture() {
            const examenId = $('#examen_id').val();
            const prixReduit = parseFloat($('#prix_reduit').val()) || 0;
            const avance = parseFloat($('#avance').val()) || 0;

            // Fetch the selected examen price from the dropdown
            const totalPrix = parseFloat($('.dropdown-search-content.examen a[data-id="' + examenId + '"]').data('prix'));

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
    </script>
</head>
<body>
    <h2>Edit Prelevement for <?php echo htmlspecialchars($prelevement_data['patient_id']); ?></h2>
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
        <div class="dropdown-search">
            <input type="text" id="search_examen" placeholder="Search Examen" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>">
            <div class="dropdown-search-content examen">
                <?php foreach ($examens as $examen): ?>
                    <a href="#" data-id="<?php echo htmlspecialchars($examen['examen_id']); ?>" data-prix="<?php echo htmlspecialchars($examen['prix']); ?>"><?php echo htmlspecialchars($examen['sub_type']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" id="examen_id" name="examen_id" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>" required><br>

        <h3>Facture</h3>
        <label>Total Prix:</label><input type="text" id="total_prix" value="<?php echo htmlspecialchars($facture_data['total_prix']); ?>" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" value="<?php echo htmlspecialchars($facture_data['prix_reduit']); ?>" onchange="updateFacture()" required><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" value="<?php echo htmlspecialchars($facture_data['avance']); ?>" onchange="updateFacture()" required><br>
        <label>Montant Du:</label><input type="text" id="montant_du" value="<?php echo htmlspecialchars($facture_data['montant_du']); ?>" readonly><br>
        <label>Rest:</label><input type="text" id="rest" value="<?php echo htmlspecialchars($facture_data['rest']); ?>" readonly><br>
        
        <button type="submit" name="update_prelevement">Update</button>
    </form>
    <form method="get" action="create_prelevement_assistant.php">
        <button type="submit">Back to Create Prelevement</button>
    </form>
</body>
</html>
