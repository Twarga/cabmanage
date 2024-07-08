<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'Template.php';
require_once 'Patient.php';
require_once 'DocteurExterieur.php';
require_once 'Examen.php';
require('vendor/setasign/fpdf/fpdf.php'); // Adjust this path to where you placed the FPDF library

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$template = new Template($db);
$patient = new Patient($db);
$docteur = new DocteurExterieur($db);
$examen = new Examen($db);

// Get the prelevement ID from the URL
$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);
if (!$prelevement_data) {
    die('ERROR: Prelevement not found.');
}

// Fetch patient data using patient_id from prelevement_data
$patient_data = $patient->readOne($prelevement_data['patient_id']);
if (!$patient_data) {
    die('ERROR: Patient not found.');
}

// Fetch user data for the created_by field
$created_by_user = 'N/A';
if (isset($prelevement_data['created_by'])) {
    $created_by_user_data = $db->query("SELECT name, prenom FROM users WHERE user_id = " . intval($prelevement_data['created_by']))->fetch_assoc();
    if ($created_by_user_data) {
        $created_by_user = $created_by_user_data['name'] . " " . $created_by_user_data['prenom'];
    }
}

// Fetch doctor data
$doctor_name = 'N/A';
if (isset($prelevement_data['docteur_exterieur_id'])) {
    $doctor_data = $docteur->readOne($prelevement_data['docteur_exterieur_id']);
    if ($doctor_data) {
        $doctor_name = $doctor_data['full_name'];
    }
}

// Fetch exams data
$examens_list = $examen->readAllByPrelevementNumber($prelevement_data['prelevement_id']);

// Fetch the rapport_txt content
$rapport_txt = isset($prelevement_data['rapport_txt']) ? strip_tags($prelevement_data['rapport_txt']) : 'N/A';

// Create instance of FPDF class
class PDF extends FPDF {
    function Header() {
        // Placeholder for header content
        $this->SetFont('Arial', 'I', 12);
        $this->Cell(0, 10, 'Header Placeholder', 0, 1, 'C');
        $this->Ln(10); // Add some space after header
    }

    function Footer() {
        // Placeholder for "Edité le" and "Signé" section
        $this->SetY(-70);
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Edit le : ' . date('Y-m-d'), 0, 0, 'L');
        $this->Cell(0, 10, 'Sign : Dr.  Maliki malika Lalla', 0, 0, 'R');
    }
}

$pdf = new PDF();

// Add a page
$pdf->AddPage();

// Set font and position for the report content
$pdf->SetFont('Arial', '', 12);

// Patient and report details
$rightColumn = "PATIENT: " . strtoupper($patient_data['name'] . " " . $patient_data['prenom']) . "\n";
$rightColumn .= "CODE PATIENT: " . $prelevement_data['patient_id'] . "\n";
$rightColumn .= "MEDECIN TRAITANT: " . strtoupper($doctor_name) . "\n";
$rightColumn .= "RECUE LE: " . $prelevement_data['date_reception'] . "\n";
$rightColumn .= "N/REF: " . $prelevement_id . "\n";

// Print the right column data
$pdf->SetXY(150, 30); // Adjust the X and Y positions to align to the right
$pdf->MultiCell(0, 10, $rightColumn);

// Print the title
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetXY(10, 90);
$pdf->Cell(0, 10, 'COMPTE RENDU ANATOMO-PATHOLOGIQUE', 0, 1, 'C');

// Print the report text
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(10, 110);
$pdf->MultiCell(0, 10, $rapport_txt);

// Output the PDF in a way that pops out the print dialog
$pdf->Output('I', 'rapport.pdf');
?>
