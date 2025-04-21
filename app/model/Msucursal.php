<?php

require_once(__DIR__ . '/../config/database.php'); 

class Msucursal {
    private $db;
    
    public $id;
    public $nombre;
    public $ubicacion;
    public $bombas;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }
    
    public function obtenerSucursales() {
        $query = "SELECT * FROM sucursal";
        $result = $this->db->query($query);
        
        return $result;
    }

    public function crearSucursal($nombre, $ubicacion, $bombas) {
        $query = "INSERT INTO sucursal (nombre, ubicacion, bombas) VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssi", $nombre, $ubicacion, $bombas);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    public function obtenerSucursalPorId($id) {
        $query = "SELECT * FROM sucursal WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function actualizarSucursal($id, $nombre, $ubicacion, $bombas) {
        $query = "UPDATE sucursal SET nombre = ?, ubicacion = ?, bombas = ? WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssii", $nombre, $ubicacion, $bombas, $id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    public function eliminarSucursal($id) {
        $query = "DELETE FROM sucursal WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }

    public function obtenerTanquesSucursal($id) {
        $query = "SELECT * FROM sucursal WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta: " . $this->db->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

}
