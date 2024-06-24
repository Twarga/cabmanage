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

// Function to generate random data
function randomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function to generate random date of birth
function randomDate() {
    $start = strtotime("1950-01-01");
    $end = strtotime("2023-01-01");
    $timestamp = mt_rand($start, $end);
    return date("Y-m-d", $timestamp);
}

// Number of users to create
$numUsers = 1000; // Change this number to the desired amount of users to create

for ($i = 0; $i < $numUsers; $i++) {
    $name = randomString(8);
    $prenom = randomString(8);
    $date_naissance = randomDate();
    $age = date_diff(date_create($date_naissance), date_create('today'))->y;
    $type_identification = 'Passeport'; // Change this if needed
    $identification_number = randomString(10);
    $email = randomString(5) . '@example.com';
    $phone_number = '062' . rand(1000000, 9999999);
    $situation_familiale = 'Célibataire'; // Change this if needed
    $sexe = 'Homme'; // Change this if needed
    $adresse = randomString(20);
    $type_assurance = 'CNOPS'; // Change this if needed
    $numero_assurance = randomString(12);

    try {
        // Insert patient data
        $patient->create($name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance);
        echo "Created patient: $name $prenom\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
