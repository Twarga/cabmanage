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
require_once 'Examen.php'; 
require_once 'DocteurExterieur.php'; // Include the DocteurExterieur class

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);
$examen = new Examen($db); 
$docteurExterieur = new DocteurExterieur($db); // Initialize the DocteurExterieur class

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

// Fetch all examens
$examens = $examen->read(); // Fetch all examens

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
            $facture->total_prix = $_POST['total_prix']; // Use the value passed from the form
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

        .report-section {
            padding: 0px;
        }

        .report-section h2 {
            text-align: center;
            color: #00E6FF;
        }

        .report-content {
            height: 150px;
            background-color: #004B5B;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
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

            $('#search_template').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.template a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_template').on('focus', function () {
                $('.dropdown-search-content.template').show();
            });

            $('#search_template').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.template').hide();
                }, 200);
            });

            $('.dropdown-search-content.template a').on('click', function () {
                $('#search_template').val($(this).text());
                $('#rapport_template').val($(this).data('id'));
                $('.dropdown-search-content.template').hide();
                loadTemplate();
            });
        });

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
                window.location.href = 'delete_prelevement.php?id=' + prelevement_id + '&patient_id=' + patient_id;
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
    <div class="container">
        <button id="retour" onclick="window.history.back()">
            <i class="retour-btn"><img src="Front/imag/left-arrow.png"></i>
        </button>
        <form method="post" enctype="multipart/form-data" action="update_prelevement.php?id=<?php echo $prelevement_id; ?>">
            <div class="form-section">
                <div class="left-form">
                    <h2>Prélèvement : <?php echo htmlspecialchars($prelevement_data['patient_id']); ?></h2>
                    <div class="form-group">
                        <label for="typePrelevement">Type Prélèvement</label>
                        <select id="typePrelevement" name="type_prelevement" required>
                            <option value="Biopsie" <?php if ($prelevement_data['type_prelevement'] == 'Biopsie') echo 'selected'; ?>>Biopsie</option>
                            <option value="Cytologie" <?php if ($prelevement_data['type_prelevement'] == 'Cytologie') echo 'selected'; ?>>Cytologie</option>
                            <option value="Pièce opératoire" <?php if ($prelevement_data['type_prelevement'] == 'Pièce opératoire') echo 'selected'; ?>>Pièce opératoire</option>
                            <option value="Immuno Histochimique" <?php if ($prelevement_data['type_prelevement'] == 'Immuno Histochimique') echo 'selected'; ?>>Immuno Histochimique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateReception">Date Réception</label>
                        <input type="date" id="dateReception" name="date_reception" value="<?php echo htmlspecialchars($prelevement_data['date_reception']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="dateCreation">Date Création</label>
                        <input type="date" id="dateCreation" name="date_creation" value="<?php echo htmlspecialchars($prelevement_data['date_creation']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nombreFlacons">Nombre de flacons</label>
                        <input type="number" id="nombreFlacons" name="nombre_flacons" value="<?php echo htmlspecialchars($prelevement_data['nombre_flacons']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pieceJointe">Ordonnance</label>
                        <input type="file" id="pieceJointe" name="ordonnance">
                    </div>
                    <div class="form-group">
                        <label for="medecin">Médecin</label>
                        <input type="number" id="medecin" name="docteur_exterieur_id" value="<?php echo htmlspecialchars($prelevement_data['docteur_exterieur_id']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="examen">Examen</label>
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
                        <label for="total_prix">Total Prix</label>
                        <input type="text" id="total_prix" name="total_prix" value="<?php echo htmlspecialchars($facture_data['total_prix']); ?>" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="prix_reduit">Prix Réduit</label>
                        <input type="number" id="prix_reduit" name="prix_reduit" value="<?php echo htmlspecialchars($facture_data['prix_reduit']); ?>" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="avance">Avance</label>
                        <input type="number" id="avance" name="avance" value="<?php echo htmlspecialchars($facture_data['avance']); ?>" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="montant_du">Montant Du</label>
                        <input type="text" id="montant_du" name="montant_du" value="<?php echo htmlspecialchars($facture_data['montant_du']); ?>" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="rest">Reste</input type="text" id="rest" name="rest" value="<?php echo htmlspecialchars($facture_data['rest']); ?>" readonly>
                    </div>
                </div>
            </div>
            <div class="report-section">
                <h2>Rapport</h2>
                <div class="form-group">
                    <label for="rapportTemplate">Rapport Template</label>
                    <div class="dropdown-search">
                        <input type="text" id="search_template" placeholder="Search Template">
                        <div class="dropdown-search-content template">
                            <?php foreach ($templates as $template): ?>
                                <a href="#" data-id="<?php echo htmlspecialchars($template['template_id']); ?>"><?php echo htmlspecialchars($template['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <input type="hidden" id="rapport_template" name="rapport_template">
                </div>
                <textarea name="rapport_txt" id="rapport_txt"><?php echo htmlspecialchars($prelevement_data['rapport_txt']); ?></textarea>
                <script>
                    CKEDITOR.replace('rapport_txt');
                </script>
                <div class="report-buttons">
                    <button type="submit" name="update_prelevement">Update</button>
                    <button type="button" onclick="saveTemplate()">Enregistrer un modèle</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
