<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';
require_once 'Prelevement.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$patient = new Patient($db);
$prelevement = new Prelevement($db);
$facture = new Facture($db);

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Patient ID not found.');

// Fetch patient data
$patient_data = $patient->readOne($patient_id);

// Fetch prélèvements for the patient
$prelevements_history = $prelevement->readByPatient($patient_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique-Patient</title>
    <link rel="stylesheet" href="Front/histoiripatient.css">
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Front/imag/logo.png" type="image/x-icon">
    <script>
        function confirmDelete(prelevement_id, patient_id) {
            if (confirm('Are you sure you want to delete this prelevement?')) {
                window.location.href = 'delete_prelevement_view.php?id=' + prelevement_id + '&patient_id=' + patient_id;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="logo-section">
                <img src="Front/imag/logo.png" alt="Laboratory Logo" class="logo">
            </div>
            <div class="nav-buttons">
                <button class="nav-button" onclick="location.href='doctor_dashboard.php'">Tableau de bord</button>
                <button class="nav-button" onclick="location.href='patient_management.php'">Patient</button>
                <div class="user-section">
                    <img src="Front/imag/doc.jpeg" alt="User Icon" class="user-icon">
                </div>
                <button class="nav-button" onclick="location.href='prelevement_management.php'">Prélèvement</button>
                <button class="nav-button" onclick="location.href='examen.php'">Examen</button>
            </div>
            <div class="btn-logout">
                <button class="btn-logout" onclick="location.href='logout.php'"><img src="Front/imag/logout.png" alt="Logout Button Icon"></button>
            </div>
        </div>
        <div class="content-area">
            <button id="retour" onclick="window.history.back()">
                <i class="fas fa-arrow-left"><img src="Front/imag/left-arrow.png"></i>
            </button>
            <div class="patient-history">
                <h2>Historique Patient</h2>
                <div class="patient-info">
                    <div class="info-left">
                        <div class="form-group">
                            <label for="nomPrenom">Nom Prénom</label>
                            <input type="text" id="nomPrenom" value="<?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="age">Âge</label>
                            <input type="text" id="age" value="<?php echo htmlspecialchars($patient_data['age'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="pieceIdentite">Pièce identité</label>
                            <input type="text" id="pieceIdentite" value="<?php echo htmlspecialchars($patient_data['type_identification'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="situationFamiliale">Situation Familiale</label>
                            <input type="text" id="situationFamiliale" value="<?php echo htmlspecialchars($patient_data['situation_familiale'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="assurance">Assurance</label>
                            <input type="text" id="assurance" value="<?php echo htmlspecialchars($patient_data['type_assurance'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="text" id="telephone" value="<?php echo htmlspecialchars($patient_data['phone_number'] ?? ''); ?>" readonly>
                        </div>
                    </div>
                    <div class="info-right">
                        <div class="form-group">
                            <label for="neLe">Né(e) Le</label>
                            <input type="text" id="neLe" value="<?php echo htmlspecialchars($patient_data['date_naissance'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="sex">Sex</label>
                            <input type="text" id="sex" value="<?php echo htmlspecialchars($patient_data['sexe'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="numeroIdentite">N° Pièce Identité</label>
                            <input type="text" id="numeroIdentite" value="<?php echo htmlspecialchars($patient_data['identification_number'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" id="email" value="<?php echo htmlspecialchars($patient_data['email'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="immatriculation">Immatriculation</label>
                            <input type="text" id="immatriculation" value="<?php echo htmlspecialchars($patient_data['numero_assurance'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" value="<?php echo htmlspecialchars($patient_data['adresse'] ?? ''); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="prelevement">
                <h2>Prélèvement</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date Réception</th>
                            <th>Date Création</th>
                            <th>N° de Flacons</th>
                            <th>Docteur Extérieur</th>
                            <th>État de paiement</th>
                            <th>Reste</th>
                            <th>Ordonnance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prelevements_history as $history):
                            $facture_data = $facture->readOne($history['prelevement_id']); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($history['prelevement_id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($history['type_prelevement'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($history['date_reception'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($history['date_creation'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($history['nombre_flacons'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($history['docteur_exterieur_id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($facture_data['etat_paiement'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($facture_data['rest'] ?? 'N/A'); ?></td>
                                <td><?php echo $history['ordonnance'] ? '<a href="download_ordonance.php?id=' . $history['prelevement_id'] . '">Download</a>' : 'No Ordonnance'; ?></td>
                                <td>
                                    <button class="print-btn" onclick="location.href='print_prelevement.php?id=<?php echo $history['prelevement_id']; ?>'"><img src="Front/imag/printer.png" alt="Print"></button>
                                    <button class="edit-btn" onclick="location.href='edit_prelevement_assistant.php?id=<?php echo $history['prelevement_id']; ?>'"><img src="Front/imag/write.png" alt="Edit"></button>
                                    <button class="delete-btn" onclick="confirmDelete(<?php echo $history['prelevement_id']; ?>, <?php echo $patient_id; ?>)"><img src="Front/imag/delete.png" alt="Delete"></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="histoiripatient.js"></script>
</body>
</html>
