<?php
class DocteurExterieur {
    private $conn;
    private $table_name = "docteurs_exterieurs";

    // constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }


    public function readAll() {
        $query = "SELECT docteur_id, full_name FROM " . $this->table_name . " ORDER BY full_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function create($name) {
        $query = "INSERT INTO " . $this->table_name . " (full_name, phone_number) VALUES (?, NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            return $this->conn->insert_id;
        } else {
            return false;
        }
    }

    public function readOne($id) {
        $query = "SELECT full_name FROM " . $this->table_name . " WHERE docteur_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
