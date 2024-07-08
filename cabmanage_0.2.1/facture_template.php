<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';
require_once 'DocteurExterieur.php';
require_once 'Examen.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);
$docteur = new DocteurExterieur($db);
$examen = new Examen($db);

// Get parameters from URL
$prelevement_id = isset($_GET['prelevement_id']) ? intval($_GET['prelevement_id']) : die('ERROR: Prelevement ID not found.');
$patient_name = isset($_GET['patient_name']) ? $_GET['patient_name'] : 'N/A';
$demande_number = isset($_GET['demande_number']) ? $_GET['demande_number'] : 'N/A';
$date_demande = isset($_GET['date_demande']) ? $_GET['date_demande'] : 'N/A';
$doctor_name = isset($_GET['doctor_name']) ? $_GET['doctor_name'] : 'N/A';
$date_facturation = isset($_GET['date_facturation']) ? $_GET['date_facturation'] : 'N/A';
$facture_id = isset($_GET['facture_id']) ? $_GET['facture_id'] : 'N/A';
$total_price = isset($_GET['total_price']) ? $_GET['total_price'] : 'N/A';
$mode_reglement = isset($_GET['mode_reglement']) ? $_GET['mode_reglement'] : 'Espèce';

// Fetch prelevement data
$prelevement_data = $prelevement->readOne($prelevement_id);

// Fetch exams data using the examen_id from prelevement_data
$exams_list = [];
if (isset($prelevement_data['examen_id'])) {
    $exams_list = $examen->readOne($prelevement_data['examen_id']);
}

// Convert amount to words in French
function convertNumberToWords($number) {
    $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    $teen = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];

    if ($number == 0) {
        return 'zéro';
    }

    $numberInWords = '';

    // Handle the decimal part
    $integerPart = floor($number);
    $decimalPart = round(($number - $integerPart) * 100);

    if ($integerPart > 0) {
        $numberInWords .= convertIntegerToWords($integerPart);
    }

    if ($decimalPart > 0) {
        $numberInWords .= ' virgule ' . convertIntegerToWords($decimalPart);
    }

    return $numberInWords . ' DH';
}

function convertIntegerToWords($number) {
    $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
    $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
    $teen = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];

    $numberInWords = '';

    if ($number < 10) {
        return $units[$number];
    }

    if ($number < 20) {
        return $teen[$number - 10];
    }

    if ($number < 100) {
        $tensUnit = $tens[floor($number / 10)];
        $unit = $number % 10;

        if ($unit == 0) {
            return $tensUnit;
        } elseif ($number < 70 || ($number >= 80 && $number < 90)) {
            return $tensUnit . '-' . $units[$unit];
        } elseif ($number < 80) {
            return 'soixante-' . $teen[$unit];
        } else {
            return 'quatre-vingt-' . $units[$unit];
        }
    }

    if ($number < 1000) {
        $hundredsUnit = floor($number / 100);
        $remainder = $number % 100;

        if ($hundredsUnit == 1) {
            $numberInWords = 'cent';
        } else {
            $numberInWords = $units[$hundredsUnit] . ' cent';
        }

        if ($remainder > 0) {
            $numberInWords .= ' ' . convertIntegerToWords($remainder);
        }

        return $numberInWords;
    }

    if ($number < 1000000) {
        $thousandsUnit = floor($number / 1000);
        $remainder = $number % 1000;

        if ($thousandsUnit == 1) {
            $numberInWords = 'mille';
        } else {
            $numberInWords = convertIntegerToWords($thousandsUnit) . ' mille';
        }

        if ($remainder > 0) {
            $numberInWords .= ' ' . convertIntegerToWords($remainder);
        }

        return $numberInWords;
    }

    return $numberInWords;
}

// Convert total price to words
$total_price_in_words = convertNumberToWords($total_price);

// Debugging
// echo "<pre>";
// echo "Debug: Prelevement Data: ";
// print_r($prelevement_data);
// echo "Debug: Exams Data: ";
// print_r($exams_list);
// echo "</pre>";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        .invoice {
            width: 21cm; /* A4 width */
            max-width: 100%;
            padding: 1cm; /* Adding padding for print margins */
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 7cm; /* Top margin */
        }

        h1, h2, h3, h4 {
            text-align: center;
            margin: 0;
        }

        h1 {
            font-size: 1.5em;
            margin-bottom: 0px;
        }

        h2 {
            font-size: 1.2em;
            margin-bottom: 5px;
        }

        h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        h4 {
            font-size: 1.3em;
            margin-top: 30px;
            margin-bottom: 5px;
        }

        .invoice-details,
        .exams-list,
        .amount {
            margin-bottom: 20px;
        }

        .invoice-details p,
        .exams-list p,
        .amount p {
            margin: 5px 0;
        }

        .exams-list ul {
            list-style: none;
            padding: 0;
        }

        .exams-list ul li {
            margin-left: 20px;
        }

        .barcode img {
            width: 100px; /* Adjust the width to fit the space */
            height: auto; /* Maintain aspect ratio */
        }
        .footer-info {
            text-align: center;
            font-size: 12px;
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
        }
        .dotted {
            border-top: 3px  ;


        }

        @media print {
            body {
                background-color: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice {
                box-shadow: none;
                border-radius: 0;
                padding: 1cm;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <h3>FACTURE</h3>
        <p>REF : <?php echo htmlspecialchars($facture_id); ?></p>
        
        <div class="invoice-details">
            <p><strong>Patient:</strong> <?php echo htmlspecialchars($patient_name); ?></p>
            <p><strong>N° Demande:</strong> <?php echo htmlspecialchars($demande_number); ?></p>
            <p><strong>Date Demande:</strong> <?php echo htmlspecialchars($date_demande); ?></p>
            <p><strong>Médecin traitant:</strong> <?php echo htmlspecialchars($doctor_name); ?></p>
            <p><strong>Date de facturation:</strong> <?php echo htmlspecialchars($date_facturation); ?></p>
        </div>

        <div class="exams-list">
            <p><strong>Liste des examens</strong></p>
            <ul>
                <?php if (!empty($exams_list)): ?>
                    <li><?php echo htmlspecialchars($exams_list['sub_type']); ?>: <?php echo htmlspecialchars($exams_list['prix']); ?> Dirhams</li>
                <?php else: ?>
                    <li>N/A</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="amount">
            <p><strong>Montant:</strong> <?php echo htmlspecialchars($total_price); ?> Dhs</p>
            <p><strong>coefficient-P:</strong> 545</p>
        </div>

        <p>Arrêtée la présente facture à la somme de <?php echo htmlspecialchars($total_price); ?> Dirhams (<?php echo $total_price_in_words; ?>)</p>
        <p><strong>MODE DE REGLEMENT : - <?php echo htmlspecialchars($mode_reglement); ?></strong></p>

        <div class="barcode">
            <img src="Front/imag/barcode.png">
        </div>
        <div class="footer-info">
        </div>
    </div>
</body>
</html>
