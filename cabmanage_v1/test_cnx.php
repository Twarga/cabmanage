<?php
// Include the database configuration file
require_once 'config.php';

// Test the database connection
if($link) {
    echo "Connected successfully to the database.";
} else {
    echo "ERROR: Could not connect to the database.";
}

// Close the connection
mysqli_close($link);
?>
