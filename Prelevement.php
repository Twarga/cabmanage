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
    public $facture_id;

    // Constructor with DB connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create prelevement and facture
    public function create() {
        $this->conn->begin_transaction();
        try {
            // Check if the examen_id exists
            $query = "SELECT COUNT(*) AS count FROM examens WHERE examen_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $this->examen_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row['count'] == 0) {
                throw new Exception("Invalid examen_id: " . $this->examen_id);
            }

            // Create the prelevement
            $query = "INSERT INTO " . $this->table_name . " 
                      (patient_id, type_prelevement, date_reception, date_creation, nombre_flacons, ordonnance, docteur_exterieur_id, rapport_template, rapport_txt, examen_id) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("issssisiss", $this->patient_id, $this->type_prelevement, $this->date_reception, $this->date_creation, $this->nombre_flacons, $this->ordonnance, $this->docteur_exterieur_id, $this->rapport_template, $this->rapport_txt, $this->examen_id);

            if (!$stmt->execute()) {
                throw new Exception("Error creating prelevement: " . $stmt->error);
            }
            $this->prelevement_id = $stmt->insert_id;

            // Create the facture
            $query = "INSERT INTO factures (examen_id, prelevement_id, total_prix, prix_reduit, avance, montant_du, rest, etat_paiement) VALUES (?, ?, 0, 0, 0, 0, 0, 'Non payé')";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $this->examen_id, $this->prelevement_id);
            if (!$stmt->execute()) {
                throw new Exception("Error creating facture: " . $stmt->error);
            }
            $this->facture_id = $stmt->insert_id;

            // Update the prelevement with the facture_id
            $query = "UPDATE " . $this->table_name . " SET facture_id = ? WHERE prelevement_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $this->facture_id, $this->prelevement_id);
            if (!$stmt->execute()) {
                throw new Exception("Error updating prelevement with facture_id: " . $stmt->error);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            printf("Error: %s.\n", $e->getMessage());
            return false;
        }
    }

    // Read all prelevements
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Read one prelevement by ID
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE prelevement_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Read prelevements by patient ID
    public function readByPatient($patient_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Search prelevements
    public function search($term) {
        $term = "%{$term}%";
        $query = "SELECT * FROM " . $this->table_name . " WHERE type_prelevement LIKE ? OR date_reception LIKE ? OR date_creation LIKE ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $term, $term, $term);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Update prelevement
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET patient_id = ?, type_prelevement = ?, date_reception = ?, date_creation = ?, nombre_flacons = ?, ordonnance = ?, docteur_exterieur_id = ?, rapport_template = ?, rapport_txt = ?, examen_id = ?, facture_id = ? 
                  WHERE prelevement_id = ?";

        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bind_param("issssisissii", $this->patient_id, $this->type_prelevement, $this->date_reception, $this->date_creation, $this->nombre_flacons, $this->ordonnance, $this->docteur_exterieur_id, $this->rapport_template, $this->rapport_txt, $this->examen_id, $this->facture_id, $this->prelevement_id);

        if ($stmt->execute()) {
            return true;
        } else {
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

    // Delete prelevement
    public function delete($id) {
        $this->conn->begin_transaction();
        try {
            // Delete the corresponding facture
            $query = "DELETE FROM factures WHERE prelevement_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting facture: " . $stmt->error);
            }

            // Delete the prelevement
            $query = "DELETE FROM " . $this->table_name . " WHERE prelevement_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting prelevement: " . $stmt->error);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            printf("Error: %s.\n", $e->getMessage());
            return false;
        }
    }
}
?>
