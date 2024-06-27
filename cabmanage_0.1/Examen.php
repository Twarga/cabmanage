<?php
class Examen {
    private $conn;
    private $table_name = "examens";

    // Object properties
    public $examen_id;
    public $sub_type;
    public $prelevement_number;
    public $prix;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create examen
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (sub_type, prelevement_number, prix) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssd", $this->sub_type, $this->prelevement_number, $this->prix);
        return $stmt->execute();
    }

    // Read all examens
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Read one examen by ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update examen
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET sub_type = ?, prelevement_number = ?, prix = ? WHERE examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssdi", $this->sub_type, $this->prelevement_number, $this->prix, $this->examen_id);
        return $stmt->execute();
    }

    // Delete examen
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
