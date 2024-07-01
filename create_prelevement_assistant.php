<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Examen.php'; // Include the Examen class

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$examen = new Examen($db); // Initialize the Examen class

// Get the patient ID from the URL
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : die('ERROR: Patient ID not found.');

// Fetch patient data
$patient_data = $patient->readOne($patient_id);

// Fetch all examens
$examens = $examen->read(); // Fetch all examens

// Handle form submission for creating a prelevement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_prelevement'])) {
    try {
        // Set prelevement properties
        $prelevement->patient_id = $patient_id;
        $prelevement->type_prelevement = $_POST['type_prelevement'];
        $prelevement->date_reception = $_POST['date_reception'];
        $prelevement->date_creation = date('Y-m-d');
        $prelevement->nombre_flacons = $_POST['nombre_flacons'];
        $prelevement->ordonnance = $_FILES['ordonnance']['tmp_name'] ? file_get_contents($_FILES['ordonnance']['tmp_name']) : null;
        $prelevement->docteur_exterieur_id = $_POST['docteur_exterieur_id'];
        $prelevement->examen_id = $_POST['examen_id'];

        // Fetch the price of the selected examen
        $selected_examen = $examen->readOne($prelevement->examen_id);
        if (!$selected_examen) {
            throw new Exception('Selected examen not found.');
        }
        $total_prix = $selected_examen['prix'];

        // Create prelevement
        if ($prelevement->create()) {
            // Check if facture already exists for this prelevement
            $existing_facture = $facture->readOne($prelevement->prelevement_id);
            if (!$existing_facture) {
                // Set facture properties
                $facture->examen_id = $prelevement->examen_id;
                $facture->prelevement_id = $prelevement->prelevement_id;
                $facture->total_prix = $total_prix;
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

        function confirmDelete(prelevement_id, patient_id) {
            if (confirm('Are you sure you want to delete this prelevement?')) {
                window.location.href = 'delete_prelevement_assistant.php?id=' + prelevement_id + '&patient_id=' + patient_id;
            }
        }
    </script>
</head>
<body>
    <h2>Create Prelevement for <?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom']); ?></h2>
    <form method="post" enctype="multipart/form-data" action="create_prelevement_assistant.php?patient_id=<?php echo $patient_id; ?>">
        <h3>Prelevement Information</h3>
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

        <label>Examen:</label>
        <div class="dropdown-search">
            <input type="text" id="search_examen" placeholder="Search Examen">
            <div class="dropdown-search-content examen">
                <?php foreach ($examens as $examen): ?>
                    <a href="#" data-id="<?php echo htmlspecialchars($examen['examen_id']); ?>" data-prix="<?php echo htmlspecialchars($examen['prix']); ?>"><?php echo htmlspecialchars($examen['sub_type']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <input type="hidden" id="examen_id" name="examen_id" required><br>

        <h3>Facture</h3>
        <label>Total Prix:</label><input type="text" id="total_prix" readonly><br>
        <label>Prix Reduit:</label><input type="number" id="prix_reduit" name="prix_reduit" onchange="updateFacture()" required><br>
        <label>Avance:</label><input type="number" id="avance" name="avance" onchange="updateFacture()" required><br>
        <label>Montant Du:</label><input type="number" id="montant_du" name="montant_du" onchange="updateFacture()" required><br>
        <label>Rest:</label><input type="text" id="rest" readonly><br>

        <button type="submit" name="create_prelevement">Create</button>
    </form>

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
            <th>Ordonnance</th>
            <th>Edit</th>
            <th>Delete</th>
            <th>Imprime</th>
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
                <td><?php echo $history['ordonnance'] ? '<a href="download_ordonance.php?id=' . $history['prelevement_id'] . '">Download</a>' : 'No Ordonnance'; ?></td>
                <td><a href="edit_prelevement_assistant.php?id=<?php echo $history['prelevement_id']; ?>">Edit</a></td>
                <td><a href="javascript:confirmDelete(<?php echo $history['prelevement_id']; ?>, <?php echo $patient_id; ?>)">Delete</a></td>
                <td><a href="print_prelevement.php?id=<?php echo $history['prelevement_id']; ?>">Imprime</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
