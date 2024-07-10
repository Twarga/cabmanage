<?php
class Template {
    private $conn;
    private $table_name = "templates";

    public $template_id;
    public $name;
    public $content;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = "SELECT template_id, name FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE template_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create() {
        $quwwery = "INSERT INTO " . $this->table_name . " (name, content) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->name, $this->content);
        return $stmt->execute();
    }
}
?>
