<?php
class Facture {
    private $conn;
    private $table_name = "factures";

    // Object properties
    public $facture_id;
    public $examen_id;
    public $prelevement_id;
    public $total_prix;
    public $prix_reduit;
    public $avance;
    public $montant_du;
    public $rest;
    public $etat_paiement;
    public $date_paiement;
    public $date_creation;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create facture
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (examen_id, prelevement_id, total_prix, prix_reduit, avance, montant_du, rest, etat_paiement, date_creation) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            printf("Statement preparation failed: %s.\n", $this->conn->error);
            return false;
        }

        $stmt->bind_param("iiddiddss", 
                          $this->examen_id, 
                          $this->prelevement_id, 
                          $this->total_prix, 
                          $this->prix_reduit, 
                          $this->avance, 
                          $this->montant_du, 
                          $this->rest, 
                          $this->etat_paiement, 
                          $this->date_creation);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Execution failed: %s.\n", $stmt->error);
            return false;
        }
    }

    // Read all factures
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Read one facture by prelevement ID
    public function readOne($prelevement_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prelevement_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $prelevement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update facture
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET examen_id = ?, prelevement_id = ?, total_prix = ?, prix_reduit = ?, avance = ?, montant_du = ?, rest = ?, etat_paiement = ?, date_creation = ? 
                  WHERE facture_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiddiddssi", $this->examen_id, $this->prelevement_id, $this->total_prix, $this->prix_reduit, $this->avance, $this->montant_du, $this->rest, $this->etat_paiement, $this->date_creation, $this->facture_id);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error updating facture: %s.\n", $stmt->error);
            return false;
        }
    }

    

    // Delete facture
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE facture_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error deleting facture: %s.\n", $stmt->error);
            return false;
        }
    }

    // Delete factures by prelevement ID
    public function deleteByPrelevement($prelevement_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE prelevement_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $prelevement_id);
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error deleting facture by prelevement: %s.\n", $stmt->error);
            return false;
        }
    }

    // Delete factures by examen ID
    public function deleteByExamenId($examen_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE examen_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $examen_id);
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error deleting facture by examen: %s.\n", $stmt->error);
            return false;
        }
    }

    // Count factures by payment status
    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE etat_paiement = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Sum fully paid factures for today
    public function sumFullyPaidToday() {
        $query = "SELECT SUM(prix_reduit + avance) as total FROM " . $this->table_name . " WHERE etat_paiement = 'Payé' AND DATE(date_creation) = CURDATE()";
        $stmt = $this->conn->query($query);
        $row = $stmt->fetch_assoc();
        return $row['total'] ?? 0;
    }
    
    // Sum unpaid factures for today
    public function sumUnpaidToday() {
        $query = "SELECT SUM(rest) as total FROM " . $this->table_name . " WHERE etat_paiement IN ('Non payé', 'Partiellement payé') AND DATE(date_creation) = CURDATE()";
        $stmt = $this->conn->query($query);
        $row = $stmt->fetch_assoc();
        return $row['total'] ?? 0;
    }
}
?>