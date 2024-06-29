<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the necessary files
require_once 'config.php';
require_once 'Examen.php';

// Initialize the Examen class
$db = $link;
$examen = new Examen($db);

// Get the examen ID from the URL
$examen_id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Examen ID not found.');

// Fetch examen data
$examen_data = $examen->readOne($examen_id);

// Handle form submission for updating an examen
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examen->examen_id = $examen_id;
    $examen->sub_type = $_POST['sub_type'];
    $examen->prelevement_number = $_POST['prelevement_number'];
    $examen->prix = $_POST['prix'];

    if ($examen->update()) {
        echo "Examen updated successfully.<br>";
    } else {
        echo "Error updating examen.<br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Examen</title>
</head>
<body>
    <h2>Edit Examen</h2>
    <form method="post" action="edit_examen.php?id=<?php echo $examen_id; ?>">
        <label>Sub Type:</label>
        <input type="text" name="sub_type" value="<?php echo htmlspecialchars($examen_data['sub_type']); ?>" required><br>
        <label>Prelevement Number:</label>
        <input type="text" name="prelevement_number" value="<?php echo htmlspecialchars($examen_data['prelevement_number']); ?>" required><br>
        <label>Prix:</label>
        <input type="number" name="prix" value="<?php echo htmlspecialchars($examen_data['prix']); ?>" required><br>
        <button type="submit">Update</button>
    </form>
    <a href="examen.php">Back to Examen Management</a>
</body>
</html>
