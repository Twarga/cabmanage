<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Prelevement.php';
require_once 'Facture.php';

// Initialize the classes
$db = $link;
$prelevement = new Prelevement($db);
$facture = new Facture($db);

// Sample data for prelevements
$prelevements_data = [
    ['patient_id' => 1, 'type_prelevement' => 'Biopsie', 'date_reception' => '2024-06-21', 'date_creation' => date('Y-m-d'), 'nombre_flacons' => 3, 'ordonnance' => null, 'docteur_exterieur_id' => 1, 'rapport_template' => 'Template 1', 'rapport_txt' => 'Rapport 1', 'examen_id' => 1],
    ['patient_id' => 2, 'type_prelevement' => 'Cytologie', 'date_reception' => '2024-06-21', 'date_creation' => date('Y-m-d'), 'nombre_flacons' => 2, 'ordonnance' => null, 'docteur_exterieur_id' => 2, 'rapport_template' => 'Template 2', 'rapport_txt' => 'Rapport 2', 'examen_id' => 2],
    ['patient_id' => 1, 'type_prelevement' => 'Pièce opératoire', 'date_reception' => '2024-06-21', 'date_creation' => date('Y-m-d'), 'nombre_flacons' => 1, 'ordonnance' => null, 'docteur_exterieur_id' => 1, 'rapport_template' => 'Template 3', 'rapport_txt' => 'Rapport 3', 'examen_id' => 3]
];

// Insert prelevements and corresponding factures
foreach ($prelevements_data as $data) {
    // Set prelevement properties
    $prelevement->patient_id = $data['patient_id'];
    $prelevement->type_prelevement = $data['type_prelevement'];
    $prelevement->date_reception = $data['date_reception'];
    $prelevement->date_creation = $data['date_creation'];
    $prelevement->nombre_flacons = $data['nombre_flacons'];
    $prelevement->ordonnance = $data['ordonnance'];
    $prelevement->docteur_exterieur_id = $data['docteur_exterieur_id'];
    $prelevement->rapport_template = $data['rapport_template'];
    $prelevement->rapport_txt = $data['rapport_txt'];
    $prelevement->examen_id = $data['examen_id'];

    // Create prelevement
    if ($prelevement->create()) {
        // Set facture properties
        $facture->examen_id = $prelevement->examen_id;
        $facture->prelevement_id = $prelevement->prelevement_id;
        $facture->total_prix = 100.0 * $prelevement->examen_id; // Example calculation
        $facture->prix_reduit = 10.0;
        $facture->avance = 20.0;
        $facture->montant_du = $facture->total_prix - $facture->prix_reduit - $facture->avance;
        $facture->rest = $facture->montant_du;
        $facture->etat_paiement = 'Non payé';

        // Create facture
        if ($facture->create()) {
            echo "Prelevement and Facture created successfully for patient_id " . $prelevement->patient_id . ".<br>";
        } else {
            echo "Error creating facture for prelevement_id " . $prelevement->prelevement_id . ".<br>";
        }
    } else {
        echo "Error creating prelevement for patient_id " . $data['patient_id'] . ".<br>";
    }
}
?>
