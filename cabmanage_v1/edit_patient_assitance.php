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

// Fetch patient data for editing
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    $patient_data = $patient->readOne($patient_id);
}

// Handle form submission for updating patient data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $patient_id = $_POST['patient_id'];
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

        // Debug output for verification
        echo "Data before update:<br>";
        echo "name: $name<br>";
        echo "prenom: $prenom<br>";
        echo "date_naissance: $date_naissance<br>";
        echo "age: $age<br>";
        echo "type_identification: $type_identification<br>";
        echo "identification_number: $identification_number<br>";
        echo "email: $email<br>";
        echo "phone_number: $phone_number<br>";
        echo "situation_familiale: $situation_familiale<br>";
        echo "sexe: $sexe<br>";
        echo "adresse: $adresse<br>";
        echo "type_assurance: $type_assurance<br>";
        echo "numero_assurance: $numero_assurance<br>";

        // Update patient data
        $patient->update($patient_id, $name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance);
        header("Location: patient_management.php");
        exit;
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
    <title>Edit Patient</title>
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Front/imag/logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1F4D5A;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Hide the overall scrollbar */
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .content-area {
            flex: 1;
            width: 90%;
            margin-top: 20px;
            padding: 20px;
            background-color: #088696;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            text-align: center;
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

        h1 {
            text-align: left;
            font-size: 24px;
            color: #00E6FF;
            margin-bottom: 20px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        h2 {
            text-align: center;
            color: #00E6FF;
        }

        .form-group {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 150px;
            color: #ffffff;
            text-align: right;
            margin-right: 10px;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: none;
            border-radius: 5px;
        }

        .submit-btn {
            background-color: #00bfbf;
            color: #fff;
            border: none;
            padding: 10px 50px;
            cursor: pointer;
            border-radius: 120px;
            display: block;
            margin: 10px auto;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #009f9f;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .form-group input {
                width: 70%;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 100%;
            }

            #retour img {
                width: 20px;
                height: 20px;
            }

            .form-group input {
                padding: 6px;
            }
        }
    </style>
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
                    <img src="Front/imag/inf.jpeg" alt="User Icon" class="user-icon">
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
            <h1>Edit Patient</h1>
            <div class="form-section">
                <h2>Modifier Patient</h2>
                <form method="post" action="edit_patient.php">
                    <input type="hidden" name="patient_id" value="<?php echo $patient_data['patient_id']; ?>">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($patient_data['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prenom:</label>
                        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($patient_data['prenom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="date_naissance">Date Naissance:</label>
                        <input type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($patient_data['date_naissance']); ?>" required onchange="calculateAge()">
                    </div>
                    <div class="form-group">
                        <label for="age">Age:</label>
                        <input type="text" id="age" name="age" value="<?php echo htmlspecialchars($patient_data['age']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="type_identification">Type Identification:</label>
                        <select id="type_identification" name="type_identification" required>
                            <option value="Passeport" <?php if($patient_data['type_identification'] == 'Passeport') echo 'selected'; ?>>Passeport</option>
                            <option value="Carte d'identité" <?php if($patient_data['type_identification'] == 'Carte d\'identité') echo 'selected'; ?>>Carte d'identité</option>
                            <option value="Permis de conduire" <?php if($patient_data['type_identification'] == 'Permis de conduire') echo 'selected'; ?>>Permis de conduire</option>
                            <option value="Carte de séjour" <?php if($patient_data['type_identification'] == 'Carte de séjour') echo 'selected'; ?>>Carte de séjour</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="identification_number">Identification Number:</label>
                        <input type="text" id="identification_number" name="identification_number" value="<?php echo htmlspecialchars($patient_data['identification_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($patient_data['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number:</label>
                        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($patient_data['phone_number']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="situation_familiale">Situation Familiale:</label>
                        <select id="situation_familiale" name="situation_familiale" required>
                            <option value="Marié" <?php if($patient_data['situation_familiale'] == 'Marié') echo 'selected'; ?>>Marié</option>
                            <option value="Célibataire" <?php if($patient_data['situation_familiale'] == 'Célibataire') echo 'selected'; ?>>Célibataire</option>
                            <option value="Veuf" <?php if($patient_data['situation_familiale'] == 'Veuf') echo 'selected'; ?>>Veuf</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sexe">Sexe:</label>
                        <select id="sexe" name="sexe" required>
                            <option value="Femme" <?php if($patient_data['sexe'] == 'Femme') echo 'selected'; ?>>Femme</option>
                            <option value="Homme" <?php if($patient_data['sexe'] == 'Homme') echo 'selected'; ?>>Homme</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse:</label>
                        <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($patient_data['adresse']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="type_assurance">Type Assurance:</label>
                        <select id="type_assurance" name="type_assurance" required>
                            <option value="CNOPS" <?php if($patient_data['type_assurance'] == 'CNOPS') echo 'selected'; ?>>CNOPS</option>
                            <option value="CNSS" <?php if($patient_data['type_assurance'] == 'CNSS') echo 'selected'; ?>>CNSS</option>
                            <option value="MAFAR" <?php if($patient_data['type_assurance'] == 'MAFAR') echo 'selected'; ?>>MAFAR</option>
                            <option value="SAHAM" <?php if($patient_data['type_assurance'] == 'SAHAM') echo 'selected'; ?>>SAHAM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="numero_assurance">Numero Assurance:</label>
                        <input type="text" id="numero_assurance" name="numero_assurance" value="<?php echo htmlspecialchars($patient_data['numero_assurance']); ?>" required>
                    </div>
                    <button class="submit-btn" type="submit">Update</button>
                </form>
            </div>
            <br>

        </div>
    </div>
</body>
</html>
