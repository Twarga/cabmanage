<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
    <link rel="icon" href="E:\Projetsliwi\imag\logo.png" type="image/x-icon">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="E:\Projetsliwi\imag\logo.png" alt="Logo" class="logo">
        </div>
        <form class="login-form" action="login.php" method="post">
            <input type="email" name="email" placeholder="connexion" class="input-field" required>
            <input type="password" name="password" placeholder="Mot de Passe" class="input-field" required>
            <button type="submit" class="login-button">Se connecter</button>
        </form>

        <?php
        // Include the necessary files
        require_once 'config.php';
        require_once 'auth.php';

        // Initialize the Auth class
        $db = $link;
        $auth = new Auth($db);

        // Handle login form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $auth->login($email, $password);

            if ($user) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = $user['type_user'];

                // Redirect to the appropriate dashboard
                if ($user['type_user'] == 'Docteur') {
                    header("Location: doctor_dashboard.php");
                } else if ($user['type_user'] == 'Assistant') {
                    header("Location: assistant_dashboard.php");
                } else {
                    header("Location: admin_dashboard.php");
                }
                exit;
            } else {
                echo "<p>Invalid email or password.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
