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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $template->name = $_POST['template_name'];
    $template->content = $_POST['template_content'];
    
    if ($template->create()) {
        echo "Template saved successfully.";
    } else {
        echo "Error saving template.";
    }
}
?>
