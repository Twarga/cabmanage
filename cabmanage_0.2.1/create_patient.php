<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Patient.php';

// Initialize the Patient class
$db = $link;
$patient = new Patient($db);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = $_POST['name'];
        $prenom = $_POST['prenom'];
        $date_naissance = $_POST['date_naissance'];
        $age = $_POST['age'];
        $type_identification = $_POST['type_identification'];
        $identification_number = $_POST['identification_number'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $situation_familiale = $_POST['situation_familiale'];
        $sexe = $_POST['sexe'];
        $adresse = $_POST['adresse'];
        $type_assurance = $_POST['type_assurance'];
        $numero_assurance = $_POST['numero_assurance'];

        // Check if patient already exists
        if ($patient->exists($identification_number, $email)) {
            echo "Error: Patient with this ID or email already exists.";
        } else {
            // Insert patient data
            $patient->create($name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance);
            header("Location: patient_management.php");
            exit;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Patient</title>
    <link rel="stylesheet" href="Front/nouveaupatient.css">
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Fron/imag/logo.png" type="image/x-icon">
    <script>
        function calculateAge() {
            const dob = document.getElementById('date_naissance').value;
            if (dob) {
                const today = new Date();
                const birthDate = new Date(dob);
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();
                const dayDifference = today.getDate() - birthDate.getDate();

                if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
                    age--;
                }

                let ageText = '';
                if (age < 10) {
                    const months = monthDifference < 0 ? monthDifference + 12 : monthDifference;
                    ageText = `${age} years ${months} months`;
                } else {
                    ageText = `${age} years`;
                }

                document.getElementById('age').value = ageText;
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
            <form class="patient-form" method="post" action="create_patient.php">
                <h2>Nouveau Patient</h2>
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" required>
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="date_naissance">Né(e) Le</label>
                    <input type="date" id="date_naissance" name="date_naissance" required onchange="calculateAge()">
                    <label for="age">Âge</label>
                    <input type="text" id="age" name="age" readonly>
                </div>
                <div class="form-group">
                    <label for="type_identification">Pièce identité</label>
                    <select id="type_identification" name="type_identification" required>
                        <option value="">Sélectionner Pièce Identité</option>
                        <option value="Passeport">Passeport</option>
                        <option value="Carte d'identité">Carte d'identité</option>
                        <option value="Permis de conduire">Permis de conduire</option>
                        <option value="Carte de séjour">Carte de séjour</option>
                    </select>
                    <label for="identification_number">N°Pièce Identité</label>
                    <input type="text" id="identification_number" name="identification_number" required>
                </div>
                <div class="form-group">
                    <label for="situation_familiale">Situation Familiale</label>
                    <select id="situation_familiale" name="situation_familiale" required>
                        <option value="">Sélectionner Situation</option>
                        <option value="Marié">Marié</option>
                        <option value="Célibataire">Célibataire</option>
                        <option value="Veuf">Veuf</option>
                    </select>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="type_assurance">Assurance</label>
                    <select id="type_assurance" name="type_assurance" required>
                        <option value="">Sélectionner Assurance</option>
                        <option value="CNOPS">CNOPS</option>
                        <option value="CNSS">CNSS</option>
                        <option value="MAFAR">MAFAR</option>
                        <option value="SAHAM">SAHAM</option>
                    </select>
                    <label for="numero_assurance">N° Assurance</label>
                    <input type="text" id="numero_assurance" name="numero_assurance" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Téléphone</label>
                    <input type="text" id="phone_number" name="phone_number" required>
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>
                <div class="form-group center-align">
                    <label for="sexe">Sexe</label>
                    <select id="sexe" name="sexe" required>
                        <option value="Femme">Femme</option>
                        <option value="Homme">Homme</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="reset" class="btn-reset">Annuler</button>
                    <button type="submit" class="btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
