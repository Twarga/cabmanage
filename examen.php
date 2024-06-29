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

// Handle form submission for creating an exam
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_examen'])) {
    try {
        // Validate input size
        $sub_type = $_POST['sub_type'];
        if (strlen($sub_type) > 255) {
            throw new Exception('Sub Type must be 255 characters or less.');
        }

        // Set examen properties
        $examen->sub_type = $sub_type;
        $examen->prelevement_number = $_POST['prelevement_number'];
        $examen->prix = $_POST['prix'];

        // Create examen
        if ($examen->create()) {
            echo "Examen created successfully.<br>";
        } else {
            echo "Error creating examen.<br>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all examens
$examens = $examen->read();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examen Management</title>
</head>
<body>
    <h2>Create Examen</h2>
    <form method="post" action="examen.php">
        <label>Sub Type:</label>
        <input type="text" name="sub_type" required><br>
        <label>Prelevement Number:</label>
        <input type="text" name="prelevement_number" required><br>
        <label>Prix:</label>
        <input type="number" step="0.01" name="prix" required><br>
        <button type="submit" name="create_examen">Create</button>
    </form>

    <h2>Examen List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Sub Type</th>
            <th>Prelevement Number</th>
            <th>Prix</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($examens as $exam) { ?>
            <tr>
                <td><?php echo htmlspecialchars($exam['examen_id']); ?></td>
                <td><?php echo htmlspecialchars($exam['sub_type']); ?></td>
                <td><?php echo htmlspecialchars($exam['prelevement_number']); ?></td>
                <td><?php echo htmlspecialchars($exam['prix']); ?></td>
                <td>
                    <a href="edit_examen.php?id=<?php echo $exam['examen_id']; ?>">Edit</a>
                    <a href="delete_examen.php?id=<?php echo $exam['examen_id']; ?>" onclick="return confirm('Are you sure you want to delete this examen?')">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
