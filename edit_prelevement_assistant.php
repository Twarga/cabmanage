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

// Ensure facture_data is an array
if (!$facture_data) {
    $facture_data = [
        'total_prix' => '',
        'prix_reduit' => '',
        'avance' => '',
        'montant_du' => '',
        'rest' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Prelevement</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1F4D5A;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            height: auto;
            margin: 0 auto;
            padding: 20px;
            background-color: #088696;
            border-radius: 10px;
        }

        #retour {
            border: none;
            cursor: pointer;
            background: transparent;
            border-radius: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        #retour i {
            margin-right: 0px;
        }

        #retour img {
            width: 30px;
            height: 30px;
            margin-right: 20px;
        }

        .form-section {
            display: flex;
            justify-content: space-between;
            padding: 0px;
        }

        .left-form, .right-form {
            width: 48%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select, .form-group input {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 5px;
        }

        .form-facture {
            margin-bottom: 15px;
        }

        .form-facture label {
            display: block;
            margin-bottom: 5px;
        }

        .form-facture input {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            width: 97%;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: #03e8ff82;
        }

        .right-form h2, .left-form h2 {
            text-align: center;
            color: #00E6FF;
        }

        .report-buttons {
            display: flex;
            justify-content: space-around;
            padding: 10px;
        }

        .history-section {
            padding: 0px;
        }

        .history-section h2 {
            text-align: center;
            color: #00E6FF;
        }

        .history-section table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            color: #000;
        }

        .history-section th, .history-section td {
            padding: 10px;
            text-align: center;
            border: transparent;
        }

        .history-section th {
            background-color: #00e1ff;
            color: #000;
        }

        .history-section td button {
            background: none;
            border: none;
            color: #4ACCD1;
            cursor: pointer;
            padding: 5px;
        }

        .history-section td button i {
            font-size: 18px;
        }

        .print-btn, .edit-btn, .delete-btn {
            padding: 5px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .print-btn img, .edit-btn img, .delete-btn img {
            width: 20px;
            height: 20px;
        }

        #ajouter {
            background-color: #00bfbf;
            color: #fff;
            border: none;
            padding: 10px 50px;
            cursor: pointer;
            border-radius: 120px;
            display: block;
            margin: 10px auto;
        }

        #ajouter:hover {
            background-color: #009f9f;
        }

        .dropdown-search {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-search input[type="text"] {
            width: 100%;
            padding: 8px;
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

        .action-buttons a {
            margin: 0 5px;
            color: #4ACCD1;
            text-decoration: none;
        }

        .action-buttons a:hover {
            color: #007BFF;
        }

        @media (max-width: 768px) {
            .form-section {
                flex-direction: column;
            }

            .left-form, .right-form {
                width: 100%;
            }

            .report-buttons {
                flex-direction: column;
            }

            .report-buttons button {
                width: 100%;
                margin-bottom: 10px;
            }
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
    <div class="container">
        <button id="retour" onclick="window.history.back()">
            <i class="retour-btn"><img src="Front\imag\left-arrow.png"></i>
        </button>
        <form method="post" enctype="multipart/form-data" action="update_prelevement.php?id=<?php echo $prelevement_id; ?>">
            <div class="form-section">
                <div class="left-form">
                    <h2>Edit Prelevement for <?php echo htmlspecialchars($prelevement_data['patient_id']); ?></h2>
                    <div class="form-group">
                        <label for="type_prelevement">Type Prelevement:</label>
                        <select name="type_prelevement" required>
                            <option value="Biopsie" <?php if ($prelevement_data['type_prelevement'] == 'Biopsie') echo 'selected'; ?>>Biopsie</option>
                            <option value="Cytologie" <?php if ($prelevement_data['type_prelevement'] == 'Cytologie') echo 'selected'; ?>>Cytologie</option>
                            <option value="Pièce opératoire" <?php if ($prelevement_data['type_prelevement'] == 'Pièce opératoire') echo 'selected'; ?>>Pièce opératoire</option>
                            <option value="Immuno Histochimique" <?php if ($prelevement_data['type_prelevement'] == 'Immuno Histochimique') echo 'selected'; ?>>Immuno Histochimique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_reception">Date Reception:</label>
                        <input type="date" name="date_reception" value="<?php echo htmlspecialchars($prelevement_data['date_reception']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date_creation">Date Creation:</label>
                        <input type="date" name="date_creation" value="<?php echo htmlspecialchars($prelevement_data['date_creation']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre_flacons">Nombre de flacons:</label>
                        <input type="number" name="nombre_flacons" value="<?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="ordonnance">Ordonnance:</label>
                        <input type="file" name="ordonnance">
                    </div>
                    <div class="form-group">
                        <label for="docteur_exterieur_id">Docteur Exterieur:</label>
                        <input type="number" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="search_examen">Examen:</label>
                        <div class="dropdown-search">
                            <input type="text" id="search_examen" placeholder="Search Examen" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>">
                            <div class="dropdown-search-content examen">
                                <?php foreach ($examens as $examen): ?>
                                    <a href="#" data-id="<?php echo htmlspecialchars($examen['examen_id']); ?>" data-prix="<?php echo htmlspecialchars($examen['prix']); ?>"><?php echo htmlspecialchars($examen['sub_type']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" id="examen_id" name="examen_id" value="<?php echo htmlspecialchars($prelevement_data['examen_id']); ?>" required>
                    </div>
                </div>
                <div class="right-form">
                    <h2>Facturation</h2>
                    <div class="form-facture">
                        <label for="total_prix">Total Prix:</label>
                        <input type="text" id="total_prix" name="total_prix" value="<?php echo htmlspecialchars($facture_data['total_prix']); ?>" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="prix_reduit">Prix Reduit:</label>
                        <input type="number" id="prix_reduit" name="prix_reduit" value="<?php echo htmlspecialchars($facture_data['prix_reduit']); ?>" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="avance">Avance:</label>
                        <input type="number" id="avance" name="avance" value="<?php echo htmlspecialchars($facture_data['avance']); ?>" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="montant_du">Montant Du:</label>
                        <input type="text" id="montant_du" name="montant_du" value="<?php echo htmlspecialchars($facture_data['montant_du']); ?>" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="rest">Rest:</label>
                        <input type="text" id="rest" name="rest" value="<?php echo htmlspecialchars($facture_data['rest']); ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="report-buttons">
                <button type="submit" name="update_prelevement_assistant.php">Update</button>
                <a href="create_prelevement.php?patient_id=<?php echo $prelevement_data['patient_id']; ?>"><button type="button">Back to Create Prelevement</button></a>
            </div>
        </form>
    </div>
</body>
</html>
