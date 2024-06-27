<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Template.php';

// Initialize the Template class
$db = $link;
$template = new Template($db);

// Get the template ID from the request
$template_id = isset($_GET['template_id']) ? $_GET['template_id'] : die('ERROR: Template ID not found.');

// Fetch template data
$template_data = $template->readOne($template_id);
if (!$template_data) {
    die('ERROR: Template not found.');
}

// Return the template data as JSON
echo json_encode($template_data);
?>
