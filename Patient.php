<?php
class Patient {
    private $conn;
    private $table_name = "patients"; // Define the table name

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
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
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
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'];
    }

    public function fetchPatientDetails() {
        $query = "
        SELECT 
            p.patient_id, p.name, p.prenom, 
            (SELECT COUNT(*) FROM prelevements WHERE prelevements.patient_id = p.patient_id) as prelevements,
            (SELECT SUM(total_prix) FROM factures f JOIN prelevements pl ON f.prelevement_id = pl.prelevement_id WHERE pl.patient_id = p.patient_id AND f.etat_paiement = 'Payé') as total_paid,
            (SELECT SUM(rest) FROM factures f JOIN prelevements pl ON f.prelevement_id = pl.prelevement_id WHERE pl.patient_id = p.patient_id AND f.etat_paiement = 'Non payé') as unpaid_amount
        FROM 
            patients p";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getPatientDetailsWithPrelevements() {
        $query = "
            SELECT 
                p.patient_id,
                p.name,
                p.prenom,
                COUNT(pr.prelevement_id) AS prelevement_count,
                SUM(CASE WHEN f.etat_paiement = 'Payé' THEN f.montant_du ELSE 0 END) AS total_paid,
                SUM(CASE WHEN f.etat_paiement = 'Non payé' THEN f.rest ELSE 0 END) AS total_unpaid
            FROM 
                " . $this->table_name . " p
            LEFT JOIN 
                prelevements pr ON p.patient_id = pr.patient_id
            LEFT JOIN 
                factures f ON pr.prelevement_id = f.prelevement_id
            GROUP BY 
                p.patient_id, p.name, p.prenom
            ORDER BY 
                p.patient_id DESC
        ";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
    
}

?>
