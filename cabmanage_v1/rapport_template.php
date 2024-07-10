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
require('vendor/tecnickcom/tcpdf/tcpdf.php'); // Adjust this path to where you placed the TCPDF library

// Suppress errors temporarily
error_reporting(0);

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

// Decode HTML entities to their corresponding characters
$rapport_txt = html_entity_decode($rapport_txt, ENT_QUOTES | ENT_HTML401, 'UTF-8');
$created_by_user = html_entity_decode($created_by_user, ENT_QUOTES | ENT_HTML401, 'UTF-8');
$doctor_name = html_entity_decode($doctor_name, ENT_QUOTES | ENT_HTML401, 'UTF-8');
$patient_data['name'] = html_entity_decode($patient_data['name'], ENT_QUOTES | ENT_HTML401, 'UTF-8');
$patient_data['prenom'] = html_entity_decode($patient_data['prenom'], ENT_QUOTES | ENT_HTML401, 'UTF-8');

// Restore error reporting
error_reporting(E_ALL);

// Create instance of TCPDF class
class PDF extends TCPDF {
    // We will manage header and footer manually, so we override these methods
    function Header() {
        // No header
    }

    function Footer() {
        // Reserve space for the footer (e.g., page numbers) but do not print page numbers
        $this->SetY(-20); // Reserve space for footer
    }
}

$pdf = new PDF();

// Add a page
$pdf->AddPage();

// Set font and position for the report content
$pdf->SetFont('dejavusans', '', 12);

// Patient and report details
$rightColumn = "PATIENT: " . strtoupper($patient_data['name'] . " " . $patient_data['prenom']) . "\n";
$rightColumn .= "CODE PATIENT: " . $prelevement_data['patient_id'] . "\n";
$rightColumn .= "MEDECIN TRAITANT: " . strtoupper($doctor_name) . "\n";
$rightColumn .= "REÇU LE: " . $prelevement_data['date_reception'] . "\n";
$rightColumn .= "N/REF: " . $prelevement_id . "\n";

// Print the right column data
$pdf->SetXY(150, 50); // Adjust the X and Y positions to align to the right
$pdf->MultiCell(0, 10, $rightColumn);

// Print the title
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->SetXY(10, 90);
$pdf->Cell(0, 10, 'COMPTE RENDU ANATOMO-PATHOLOGIQUE', 0, 1, 'C');

// Print the report text
$pdf->SetFont('dejavusans', '', 12);
$pdf->SetXY(10, 110);
$pdf->MultiCell(0, 10, $rapport_txt, 0, 'L', 0, 1, '', '', true, 0, false, true, 0, 'T');

// Check if there is enough space for the "Edité le" and "Signé" section
$footerHeight = 20; // Approximate height of the "Edité le" and "Signé" section
$currentY = $pdf->GetY();
$pageHeight = $pdf->getPageHeight();
$remainingSpace = $pageHeight - $currentY - $pdf->getBreakMargin() - $footerHeight - 30; // -30 for the reserved footer space

// If not enough space, add a new page
if ($remainingSpace < $footerHeight) {
    $pdf->AddPage();
}

// Print the "Edité le" and "Signé" section with spacing
$pdf->SetY($pdf->GetY() + 80); // Add some space before the section
$pdf->SetFont('dejavusans', '', 12);
$pdf->Cell(0, 10, 'Edité le : ' . date('Y-m-d') . '                                                 Signé : Dr. Maliki Malika Lalla', 0, 1, 'L');

// Output the PDF
$pdf->Output('rapport.pdf', 'I');
?>
