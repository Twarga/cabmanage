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
$success_message = '';
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
            $success_message = "Examen created successfully.";
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen</title>
    <link rel="stylesheet" href="Front/navbar.css">
    <link rel="icon" href="Front/imag/logo.png" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1F4D5A;
            color: #ffffff;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Hide the overall scrollbar */
        }

        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .content-area {
            flex: 1;
            width: 90%;
            margin-top: 20px;
            padding: 20px;
            background-color: #088696;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            text-align: center;
        }

        #retour {
            border: none;
            cursor: pointer;
            background: transparent;
            border-radius: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        #retour i {
            margin-right: 0px;
        }

        #retour img {
            width: 30px;
            height: 30px;
            margin-right: 20px;
        }

        h1 {
            text-align: left;
            font-size: 24px;
            color: #00E6FF;
            margin-bottom: 20px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        h2 {
            text-align: center;
            color: #00E6FF;
            margin-bottom: 16px;
        }

        .form-group {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            width: 150px;
            color: #ffffff;
            text-align: right;
            margin-right: 10px;
        }

        .form-group input {
            width: 300px;
            padding: 8px;
            border: none;
            border-radius: 5px;
            margin-right: 150px;

        }

        .submit-btn {
            background-color: #00bfbf;
            color: #fff;
            border: none;
            padding: 10px 50px;
            cursor: pointer;
            border-radius: 120px;
            display: block;
            margin: 10px auto;
            font-size: 16px;
        }

        .submit-btn:hover {
            background-color: #009f9f;
        }

        .table-container {
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            color: #000;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #00B1FF;
            color: #0e0d0d;
        }

        .edit-btn, .delete-btn {
            padding: 5px;
            border: none;
            background: none;
            cursor: pointer;
        }

        .edit-btn img, .delete-btn img {
            width: 20px;
            height: 20px;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            .form-group input {
                width: 70%;
            }

            th, td {
                padding: 8px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 100%;
            }

            #retour img {
                width: 20px;
                height: 20px;
            }

            .form-group input {
                padding: 6px;
            }

            th, td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div class="logo-section">
                <img src="Front/imag/logo.png" alt="Laboratory Logo" class="logo">
            </div>
            <div class="nav-buttons">
                <button class="nav-button" onclick="location.href='doctor_dashboard.php'">Tableau de bord</button>
                <button class="nav-button" onclick="location.href='patient_management.php'">Patient</button>
                <div class="user-section">
                    <img src="Front/imag/doc.jpeg" alt="User Icon" class="user-icon">
                </div>
                <button class="nav-button" onclick="location.href='prelevement_management.php'">Prélèvement</button>
                <button class="nav-button" onclick="location.href='examen.php'">Examen</button>
            </div>
            <div class="btn-logout">
                <button class="btn-logout" onclick="location.href='logout.php'"><img src="Front/imag/logout.png" alt="Logout Button Icon"></button>
            </div>            
        </div>
        <div class="content-area">
            <button id="retour" onclick="window.history.back()">
                <i class="fas fa-arrow-left"><img src="Front/imag/left-arrow.png"></i>
            </button>
            <h1>Examen</h1>
            <div class="form-section">
                <h2>Créer Examen</h2>
                <form method="post" action="examen.php">
                    <div class="form-group">
                        <label for="sub_type">Sub Type:</label>
                        <input type="text" id="sub_type" name="sub_type" required>
                    </div>
                    <div class="form-group">
                        <label for="prelevement_number">Prelevement Number:</label>
                        <input type="text" id="prelevement_number" name="prelevement_number" required>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix:</label>
                        <input type="number" step="0.01" id="prix" name="prix" required>
                    </div>
                    <button class="submit-btn" type="submit" name="create_examen">Create</button>
                </form>
            </div>
            <div class="table-container">
                <h2>Examen List</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sub Type</th>
                            <th>Prelevement Number</th>
                            <th>Prix</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examens as $exam) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($exam['examen_id']); ?></td>
                                <td><?php echo htmlspecialchars($exam['sub_type']); ?></td>
                                <td><?php echo htmlspecialchars($exam['prelevement_number']); ?></td>
                                <td><?php echo htmlspecialchars($exam['prix']); ?></td>
                                <td>
                                    <button class="edit-btn" onclick="location.href='edit_examen.php?id=<?php echo $exam['examen_id']; ?>'"><img src="Front/imag/write.png" alt="Edit"></button>
                                    <button class="delete-btn" onclick="if(confirm('Are you sure you want to delete this examen?')) location.href='delete_examen.php?id=<?php echo $exam['examen_id']; ?>'"><img src="Front/imag/delete.png" alt="Delete"></button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
