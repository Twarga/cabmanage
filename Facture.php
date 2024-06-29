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

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create facture
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (examen_id, prelevement_id, total_prix, prix_reduit, avance, montant_du, rest, etat_paiement) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bind_param("iiddidds", $this->examen_id, $this->prelevement_id, $this->total_prix, $this->prix_reduit, $this->avance, $this->montant_du, $this->rest, $this->etat_paiement);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
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
                  SET examen_id = ?, prelevement_id = ?, total_prix = ?, prix_reduit = ?, avance = ?, montant_du = ?, rest = ?, etat_paiement = ? 
                  WHERE facture_id = ?";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind values
        $stmt->bind_param("iiddiddsi", $this->examen_id, $this->prelevement_id, $this->total_prix, $this->prix_reduit, $this->avance, $this->montant_du, $this->rest, $this->etat_paiement, $this->facture_id);
    
        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
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
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }
    public function deleteByPrelevement($prelevement_id) {
        $query = "DELETE FROM factures WHERE prelevement_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $prelevement_id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

}
?>
