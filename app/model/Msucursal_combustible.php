<?php
require_once(__DIR__ . '/../config/database.php');

class Msucursal_combustible {
    private $db;

    public $sucursal_id;
    public $combustible_id;
    public $estado;
    public $fecha_actualizada;

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    public function asignarCombustible($sucursal_id, $combustible_id) {
        $this->sucursal_id = $sucursal_id;
        $this->combustible_id = $combustible_id;
        $this->estado = 'disponible';
        $this->fecha_actualizada = date('Y-m-d');

        $query = "INSERT INTO sucursal_combustible 
                  (sucursal_id, combustible_id, estado, fecha_actualizada) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iiss", 
            $this->sucursal_id, 
            $this->combustible_id, 
            $this->estado, 
            $this->fecha_actualizada
        );
        
        return $stmt->execute();
    }

    public function crearRelacion($sucursal_id, $combustible_id, $duracion_tanque = null) {
        $query = "INSERT INTO sucursal_combustible 
                 (sucursal_id, combustible_id, estado, fecha_actualizada, duracion_tanque) 
                 VALUES (?, ?, 'disponible', NOW(), ?)
                 ON DUPLICATE KEY UPDATE 
                 estado = VALUES(estado),
                 fecha_actualizada = VALUES(fecha_actualizada),
                 duracion_tanque = VALUES(duracion_tanque)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iis", $sucursal_id, $combustible_id, $duracion_tanque);
        return $stmt->execute();
    }


    public function obtenerCombustiblesPorSucursal($sucursal_id) {
        $query = "SELECT sc.*, c.tipo 
                 FROM sucursal_combustible sc
                 JOIN combustible c ON sc.combustible_id = c.id
                 WHERE sc.sucursal_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function eliminarCombustiblesDeSucursal($sucursal_id) {
        $query = "DELETE FROM sucursal_combustible WHERE sucursal_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        return $stmt->execute();
    }

    public function actualizarEstadoCombustible($sucursal_id, $combustible_id, $estado) {
        $fecha_actualizada = date('Y-m-d H:i:s');
        
        $query = "UPDATE sucursal_combustible 
                 SET estado = ?, fecha_actualizada = ?
                 WHERE sucursal_id = ? AND combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssii", $estado, $fecha_actualizada, $sucursal_id, $combustible_id);
        return $stmt->execute();
    }

}
?>
