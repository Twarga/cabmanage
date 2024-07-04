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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>
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
    <h2>Edit Patient</h2>
    <form method="post" action="edit_patient.php">
        <input type="hidden" name="patient_id" value="<?php echo $patient_data['patient_id']; ?>">
        <label>Name:</label><input type="text" name="name" value="<?php echo htmlspecialchars($patient_data['name']); ?>" required><br>
        <label>Prenom:</label><input type="text" name="prenom" value="<?php echo htmlspecialchars($patient_data['prenom']); ?>" required><br>
        <label>Date Naissance:</label><input type="date" name="date_naissance" id="date_naissance" value="<?php echo htmlspecialchars($patient_data['date_naissance']); ?>" required onchange="calculateAge()"><br>
        <label>Age:</label><input type="text" name="age" id="age" value="<?php echo htmlspecialchars($patient_data['age']); ?>" readonly><br>

        <label>Type Identification:</label>
        <select name="type_identification" required>
            <option value="Passeport" <?php if($patient_data['type_identification'] == 'Passeport') echo 'selected'; ?>>Passeport</option>
            <option value="Carte d'identité" <?php if($patient_data['type_identification'] == 'Carte d\'identité') echo 'selected'; ?>>Carte d'identité</option>
            <option value="Permis de conduire" <?php if($patient_data['type_identification'] == 'Permis de conduire') echo 'selected'; ?>>Permis de conduire</option>
            <option value="Carte de séjour" <?php if($patient_data['type_identification'] == 'Carte de séjour') echo 'selected'; ?>>Carte de séjour</option>
        </select><br>

        <label>Identification Number:</label><input type="text" name="identification_number" value="<?php echo htmlspecialchars($patient_data['identification_number']); ?>" required><br>
        <label>Email:</label><input type="email" name="email" value="<?php echo htmlspecialchars($patient_data['email']); ?>" required><br>
        <label>Phone Number:</label><input type="text" name="phone_number" value="<?php echo htmlspecialchars($patient_data['phone_number']); ?>" required><br>

        <label>Situation Familiale:</label>
        <select name="situation_familiale" required>
            <option value="Marié" <?php if($patient_data['situation_familiale'] == 'Marié') echo 'selected'; ?>>Marié</option>
            <option value="Célibataire" <?php if($patient_data['situation_familiale'] == 'Célibataire') echo 'selected'; ?>>Célibataire</option>
            <option value="Veuf" <?php if($patient_data['situation_familiale'] == 'Veuf') echo 'selected'; ?>>Veuf</option>
        </select><br>

        <label>Sexe:</label>
        <select name="sexe" required>
            <option value="Femme" <?php if($patient_data['sexe'] == 'Femme') echo 'selected'; ?>>Femme</option>
            <option value="Homme" <?php if($patient_data['sexe'] == 'Homme') echo 'selected'; ?>>Homme</option>
        </select><br>

        <label>Adresse:</label><input type="text" name="adresse" value="<?php echo htmlspecialchars($patient_data['adresse']); ?>" required><br>

        <label>Type Assurance:</label>
        <select name="type_assurance" required>
            <option value="CNOPS" <?php if($patient_data['type_assurance'] == 'CNOPS') echo 'selected'; ?>>CNOPS</option>
            <option value="CNSS" <?php if($patient_data['type_assurance'] == 'CNSS') echo 'selected'; ?>>CNSS</option>
            <option value="MAFAR" <?php if($patient_data['type_assurance'] == 'MAFAR') echo 'selected'; ?>>MAFAR</option>
            <option value="SAHAM" <?php if($patient_data['type_assurance'] == 'SAHAM') echo 'selected'; ?>>SAHAM</option>
        </select><br>

        <label>Numero Assurance:</label><input type="text" name="numero_assurance" value="<?php echo htmlspecialchars($patient_data['numero_assurance']); ?>" required><br>

        <button type="submit">Update</button>
    </form>
</body>
</html>
