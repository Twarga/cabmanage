<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
</head>
<body>
<h2>Login</h2>
<form action="login.php" method="post">
<label for="email">Email:</label>
<input type="email" name="email" required>
<br>
<label for="password">Password:</label>
<input type="password" name="password" required>
<br>
<input type="submit" value="Login">
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
</body>
</html>
