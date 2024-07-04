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

        // Bind values
        $stmt->bind_param("ssd", $this->sub_type, $this->prelevement_number, $this->prix);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
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

        // Bind values
        $stmt->bind_param("ssdi", $this->sub_type, $this->prelevement_number, $this->prix, $this->examen_id);

        if ($stmt->execute()) {
            // Update related records
            $this->updateRelatedRecords();
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

    // Update related records
    private function updateRelatedRecords() {
        // Update prelevements with the new price
        $query = "UPDATE prelevements p 
                  JOIN factures f ON p.prelevement_id = f.prelevement_id 
                  SET f.total_prix = ?, f.rest = f.total_prix - f.avance - f.prix_reduit
                  WHERE f.examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $this->prix, $this->examen_id);
        if (!$stmt->execute()) {
            printf("Error: %s.\n", $stmt->error);
        }
    }

    // Delete examen
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
}
?>
