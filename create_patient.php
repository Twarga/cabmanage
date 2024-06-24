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

        // Debug output for verification
        echo "Data before insertion:<br>";
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

        // Insert patient data
        $patient->create($name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance);
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
    <title>Create Patient</title>
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
    <h2>Create Patient</h2>
    <form method="post" action="create_patient.php">
        <label>Name:</label><input type="text" name="name" required><br>
        <label>Prenom:</label><input type="text" name="prenom" required><br>
        <label>Date Naissance:</label><input type="date" name="date_naissance" id="date_naissance" required onchange="calculateAge()"><br>
        <label>Age:</label><input type="text" name="age" id="age" readonly><br>
        
        <label>Type Identification:</label>
        <select name="type_identification" required>
            <option value="Passeport">Passeport</option>
            <option value="Carte d'identité">Carte d'identité</option>
            <option value="Permis de conduire">Permis de conduire</option>
            <option value="Carte de séjour">Carte de séjour</option>
        </select><br>
        
        <label>Identification Number:</label><input type="text" name="identification_number" required><br>
        <label>Email:</label><input type="email" name="email" required><br>
        <label>Phone Number:</label><input type="text" name="phone_number" required><br>
        
        <label>Situation Familiale:</label>
        <select name="situation_familiale" required>
            <option value="Marié">Marié</option>
            <option value="Célibataire">Célibataire</option>
            <option value="Veuf">Veuf</option>
        </select><br>
        
        <label>Sexe:</label>
        <select name="sexe" required>
            <option value="Femme">Femme</option>
            <option value="Homme">Homme</option>
        </select><br>
        
        <label>Adresse:</label><input type="text" name="adresse" required><br>
        
        <label>Type Assurance:</label>
        <select name="type_assurance" required>
            <option value="CNOPS">CNOPS</option>
            <option value="CNSS">CNSS</option>
            <option value="MAFAR">MAFAR</option>
            <option value="SAHAM">SAHAM</option>
        </select><br>
        
        <label>Numero Assurance:</label><input type="text" name="numero_assurance" required><br>
        
        <button type="submit">Create</button>
    </form>
</body>
</html>
