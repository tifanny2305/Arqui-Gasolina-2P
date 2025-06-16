<?php
require_once(__DIR__ . '/../config/database.php'); 

class Msucursal {
    private $db;
    
    private $id;
    private $nombre;
    private $ubicacion;
    private $bombas;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }
    
    // Obtener todas las sucursales
    public function obtenerSucursales() {
        try {
            $query = "SELECT * FROM sucursal ORDER BY nombre";
            $result = $this->db->query($query);
            
            if (!$result) {
                throw new Exception("Error en la consulta: " . $this->db->error);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error en obtenerSucursales: " . $e->getMessage());
            return false;
        }
    }

    // Crear nueva sucursal
    public function crearSucursal($nombre, $ubicacion, $bombas) {
        try {
            $query = "INSERT INTO sucursal (nombre, ubicacion, bombas) VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            
            $stmt->bind_param("ssi", $nombre, $ubicacion, $bombas);
            
            if ($stmt->execute()) {
                return $this->db->insert_id;
            } else {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error en crearSucursal: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener sucursal por ID
    public function obtenerSucursalPorId($id) {
        try {
            $query = "SELECT * FROM sucursal WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en obtenerSucursalPorId: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar sucursal
    public function actualizarSucursal($id, $nombre, $ubicacion, $bombas) {
        try {
            $query = "UPDATE sucursal SET nombre = ?, ubicacion = ?, bombas = ? WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            
            $stmt->bind_param("ssii", $nombre, $ubicacion, $bombas, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar sucursal
    public function eliminarSucursal($id) {
        try {
            $query = "DELETE FROM sucursal WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en eliminarSucursal: " . $e->getMessage());
            return false;
        }
    }
}