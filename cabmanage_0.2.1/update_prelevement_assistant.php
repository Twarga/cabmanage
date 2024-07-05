<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Examen.php'; // Include the Examen class
require_once 'DocteurExterieur.php'; // Include the DocteurExterieur class

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$examen = new Examen($db); // Initialize the Examen class
$docteurExterieur = new DocteurExterieur($db); // Initialize the DocteurExterieur class

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

// Fetch all external doctors
$docteurs_exterieurs = $docteurExterieur->readAll(); // Fetch all external doctors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Set prelevement properties
        $prelevement->prelevement_id = $prelevement_id;
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = $_POST['date_creation'];
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];

        // Handle ordonnance upload
        if ($_FILES['ordonnance']['tmp_name']) {
            $ordonnance_directory = 'ordonnances/';
            $ordonnance_filename = 'ordonnance_' . $prelevement_data['patient_id'] . '_' . date('Ymd') . '.pdf';
            $ordonnance_path = $ordonnance_directory . $ordonnance_filename;

            if (!is_dir($ordonnance_directory)) {
                mkdir($ordonnance_directory, 0777, true);
            }

            if (move_uploaded_file($_FILES['ordonnance']['tmp_name'], $ordonnance_path)) {
                $prelevement->ordonnance = $ordonnance_path;
            } else {
                throw new Exception('Error uploading ordonnance.');
            }
        } else {
            $prelevement->ordonnance = $prelevement_data['ordonnance'];
        }

        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->examen_id = $_POST['examen_id'];

        // Update prelevement
        if ($prelevement->update()) {
            // Set facture properties
            $facture->examen_id = $prelevement->examen_id;
            $facture->prelevement_id = $prelevement->prelevement_id;
            $facture->total_prix = $_POST['total_prix'];
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
                echo "Prelevement and Facture updated successfully for patient_id " . $prelevement->patient_id . ".<br>";
                header("Location: create_prelevement_assistant.php?patient_id={$prelevement->patient_id}");
                exit;
            } else {
                throw new Exception('Error updating facture.');
            }
        } else {
            throw new Exception('Error updating prelevement.');
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
            $('#search_docteur_exterieur').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.docteur_exterieur a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_docteur_exterieur').on('focus', function () {
                $('.dropdown-search-content.docteur_exterieur').show();
            });

            $('#search_docteur_exterieur').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.docteur_exterieur').hide();
                }, 200);
            });

            $('.dropdown-search-content.docteur_exterieur a').on('click', function () {
                $('#search_docteur_exterieur').val($(this).text());
                $('#docteur_exterieur_id').val($(this).data('id'));
                $('.dropdown-search-content.docteur_exterieur').hide();
            });

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
                window.location.href = 'delete_prelevement_assistant.php?id=' + prelevement_id;
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

        <label>Docteur Exterieur:</label>
        <div class="dropdown-search">
            <input type="text" id="search_docteur_exterieur" name="search_docteur_exterieur" placeholder="Search Docteur Exterieur" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>">
            <div class="dropdown-search-content docteur_exterieur">
                <?php foreach ($docteurs_exterieurs as $docteur_exterieur): ?>
                    <a href="#" data-id="<?php echo htmlspecialchars($docteur_exterieur['docteur_id']); ?>"><?php echo htmlspecialchars($docteur_exterieur['full_name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" id="docteur_exterieur_id" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>"><br>

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
        <label>Total Prix:</label><input type="text" id="total_prix" name="total_prix" value="<?php echo htmlspecialchars($facture_data['total_prix']); ?>" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" value="<?php echo htmlspecialchars($facture_data['prix_reduit']); ?>" onchange="updateFacture()" required><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" value="<?php echo htmlspecialchars($facture_data['avance']); ?>" onchange="updateFacture()" required><br>
        <label>Montant Du:</label><input type="text" id="montant_du" name="montant_du" value="<?php echo htmlspecialchars($facture_data['montant_du']); ?>" readonly><br>
        <label>Rest:</label><input type="text" id="rest" name="rest" value="<?php echo htmlspecialchars($facture_data['rest']); ?>" readonly><br>
        
        <button type="submit" name="update_prelevement">Update</button>
        <a href="create_prelevement_assistant.php?patient_id=<?php echo $prelevement_data['patient_id']; ?>"><button type="button">Back to Create Prelevement</button></a>
    </form>
</body>
</html>
