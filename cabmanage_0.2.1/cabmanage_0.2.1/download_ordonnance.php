<?php
require_once 'config.php';
require_once 'Prelevement.php';

$db = $link;
$prelevement = new Prelevement($db);

$prelevement_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Prelevement ID not found.');

$prelevement_data = $prelevement->readOne($prelevement_id);

if ($prelevement_data && $prelevement_data['ordonnance']) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="ordonnance_' . $prelevement_id . '.pdf"');
    echo $prelevement_data['ordonnance'];
    exit;
} else {
    die('ERROR: Ordonnance not found.');
}
?>
