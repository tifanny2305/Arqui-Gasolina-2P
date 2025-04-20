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

    //esta funcin de abajo eliminarla
    public function actualizarCapacidadTanque($sucursal_id, $combustible_id, $capacidad) {
        // Validar parámetros
        if (!is_numeric($sucursal_id) || !is_numeric($combustible_id) || !is_numeric($capacidad)) {
            error_log("Parámetros inválidos para actualizar tanque");
            return false;
        }
    
        try {
            $query = "UPDATE sucursal_combustible 
                     SET capacidad_actual = ?, fecha_actualizada = NOW() 
                     WHERE sucursal_id = ? AND combustible_id = ?";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                error_log("Error preparando consulta: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("dii", $capacidad, $sucursal_id, $combustible_id);
            
            if (!$stmt->execute()) {
                error_log("Error ejecutando actualización: " . $stmt->error);
                return false;
            }
            
            // Verificar que realmente se actualizó algún registro
            if ($stmt->affected_rows === 0) {
                error_log("No se actualizó ningún tanque. ¿Existe la relación sucursal-combustible?");
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Excepción en actualizarCapacidadTanque: " . $e->getMessage());
            return false;
        }
    }

}
