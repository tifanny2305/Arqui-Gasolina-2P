<?php
require_once(__DIR__ . '/../config/database.php');

class Mcombustible {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    public function crearCombustible($tipo, $litros) {
        $query = "INSERT INTO tipo_combustible (tipo, litros) VALUES (?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $tipo, $litros);
        return $stmt->execute();
    }

    public function obtenerCombustible() {
        $query = "SELECT * FROM tipo_combustible";
        $result = $this->db->query($query);
        return $result;
    }

    public function obtenerCombustiblePorId($id) {
        $query = "SELECT * FROM tipo_combustible WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function actualizarCombustible($id, $tipo, $litros) {
        $query = "UPDATE tipo_combustible SET tipo = ?, litros = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("sii", $tipo, $litros, $id);
        return $stmt->execute();
    }

    public function eliminarCombustible($id) {
        $query = "DELETE FROM tipo_combustible WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
