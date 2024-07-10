<?php
// Auth.php
require_once 'config.php';

class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Register a new user
    public function register($name, $prenom, $phone, $email, $password, $type_user) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Prepare the SQL statement
        $sql = "INSERT INTO users (name, prenom, phone_number, email, password, type_user) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $prenom, $phone, $email, $hashed_password, $type_user);
        // Execute the statement and return the result
        return $stmt->execute();
    }

    // Login a user
    public function login($email, $password) {
        // Prepare the SQL statement
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, return the user data
            return $user;
        } else {
            // Password is incorrect or user does not exist
            return false;
        }
    }
}
?>
