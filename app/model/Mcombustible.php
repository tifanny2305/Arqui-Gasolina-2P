<?php
require_once(__DIR__ . '/../config/database.php');

class Mcombustible {
    private $db;

    // Atributos públicos del combustible
    public $id;
    public $tipo;

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Crear nuevo combustible
    public function crearCombustible($tipo) {
        $query = "INSERT INTO combustible (tipo) VALUES (?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $tipo);
        return $stmt->execute();
    }

    // Obtener todos los combustibles
    public function obtenerCombustible() {
        $query = "SELECT * FROM combustible";
        $result = $this->db->query($query);
        return $result;
    }

    // Obtener un combustible por su ID
    public function obtenerCombustiblePorId($id) {
        $query = "SELECT * FROM combustible WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Actualizar un combustible
    public function actualizarCombustible($id, $tipo) {
        $query = "UPDATE combustible SET tipo = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $tipo, $id);
        return $stmt->execute();
    }

    // Eliminar un combustible
    public function eliminarCombustible($id) {
        $query = "DELETE FROM combustible WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

