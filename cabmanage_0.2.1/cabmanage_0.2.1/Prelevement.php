<?php
class Prelevement {
    private $conn;
    private $table_name = "prelevements";

    // Object properties
    public $prelevement_id;
    public $patient_id;
    public $type_prelevement;
    public $date_reception;
    public $date_creation;
    public $nombre_flacons;
    public $ordonnance;
    public $docteur_exterieur_id;
    public $rapport_template;
    public $rapport_txt;
    public $examen_id;
    public $barcode; // Add barcode property

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (patient_id, type_prelevement, date_reception, date_creation, nombre_flacons, ordonnance, docteur_exterieur_id, rapport_template, rapport_txt, examen_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bind_param("isssisssss", $this->patient_id, $this->type_prelevement, $this->date_reception, $this->date_creation, $this->nombre_flacons, $this->ordonnance, $this->docteur_exterieur_id, $this->rapport_template, $this->rapport_txt, $this->examen_id);
    
        if ($stmt->execute()) {
            $this->prelevement_id = $this->conn->insert_id;
            return true;
        } else {
            return false;
        }
    }
    

    // Update barcode
    public function updateBarcode($prelevement_id, $barcode_path) {
        $query = "UPDATE " . $this->table_name . " SET barcode = ? WHERE prelevement_id = ?";

        $stmt = $this->conn->prepare($query);

        $barcode_path = htmlspecialchars(strip_tags($barcode_path));
        $prelevement_id = htmlspecialchars(strip_tags($prelevement_id));

        $stmt->bind_param("si", $barcode_path, $prelevement_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Read one prelevement by ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prelevement_id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Read all prelevements by patient ID
    public function readByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if a prelevement already exists for a given patient ID
    public function existsForPatient($patient_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update prelevement
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET patient_id = ?, type_prelevement = ?, date_reception = ?, date_creation = ?, nombre_flacons = ?, ordonnance = ?, docteur_exterieur_id = ?, rapport_template = ?, rapport_txt = ?, examen_id = ? 
                  WHERE prelevement_id = ?";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind values
        $stmt->bind_param("isssissssii", $this->patient_id, $this->type_prelevement, $this->date_reception, $this->date_creation, $this->nombre_flacons, $this->ordonnance, $this->docteur_exterieur_id, $this->rapport_template, $this->rapport_txt, $this->examen_id, $this->prelevement_id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

    // Delete prelevement
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE prelevement_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total'];
    }

}
?>
