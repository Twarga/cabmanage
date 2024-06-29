<?php
class Patient {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
// Add this method to the Prelevement class if not already present
    public function readByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // Create a new patient
    public function create($name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance) {
        $sql = "INSERT INTO patients (name, prenom, date_naissance, age, type_identification, identification_number, email, phone_number, situation_familiale, sexe, adresse, type_assurance, numero_assurance) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssisssssssss", $name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance);
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    }

    // Read all patients
    public function read() {
        $sql = "SELECT * FROM patients";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search for patients
    public function search($term) {
        $term = "%{$term}%";
        $sql = "SELECT * FROM patients WHERE identification_number LIKE ? OR name LIKE ? OR date_naissance LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $term, $term, $term);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Read a single patient by ID
    public function readOne($id) {
        $sql = "SELECT * FROM patients WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update a patient
    public function update($id, $name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance) {
        $sql = "UPDATE patients SET name = ?, prenom = ?, date_naissance = ?, age = ?, type_identification = ?, identification_number = ?, email = ?, phone_number = ?, situation_familiale = ?, sexe = ?, adresse = ?, type_assurance = ?, numero_assurance = ? WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssisssssssssi", $name, $prenom, $date_naissance, $age, $type_identification, $identification_number, $email, $phone_number, $situation_familiale, $sexe, $adresse, $type_assurance, $numero_assurance, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("Error: " . $stmt->error);
        }
    }

    // Delete a patient
    public function delete($id) {
        $sql = "DELETE FROM patients WHERE patient_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function exists($identification_number, $email) {
        $query = "SELECT * FROM patients WHERE identification_number = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $identification_number, $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
    
    public function readAll() {
        $query = "SELECT * FROM patients ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $patients = array();
        while ($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
        return $patients;
    }
    
    
}

?>
