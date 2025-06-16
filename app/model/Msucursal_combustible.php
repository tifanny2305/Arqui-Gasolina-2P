<?php
require_once(__DIR__ . '/../config/database.php');

class Msucursal_combustible {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Asignar combustible a sucursal
    public function asignarCombustible($sucursal_id, $combustible_id) {
        try {
            // Verificar si ya existe la relación
            $check_query = "SELECT id FROM sucursal_combustible WHERE sucursal_id = ? AND combustible_id = ?";
            $check_stmt = $this->db->prepare($check_query);
            $check_stmt->bind_param("ii", $sucursal_id, $combustible_id);
            $check_stmt->execute();
            $exists = $check_stmt->get_result()->fetch_assoc();

            if ($exists) {
                return true; // Ya existe, no necesita crear
            }

            // Crear nueva relación
            $query = "INSERT INTO sucursal_combustible (sucursal_id, combustible_id) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $sucursal_id, $combustible_id);
            
            if ($stmt->execute()) {
                $sucursal_combustible_id = $this->db->insert_id;
                
                // Crear almacenamiento inicial
                $this->crearAlmacenamientoInicial($sucursal_combustible_id);
                
                return true;
            }
            return false;

        } catch (Exception $e) {
            error_log("Error en asignarCombustible: " . $e->getMessage());
            return false;
        }
    }

    // Crear almacenamiento inicial
    private function crearAlmacenamientoInicial($sucursal_combustible_id, $capacidad_inicial = 0) {
        $query = "INSERT INTO almacenamiento (sucursal_combustible_id, cap_actual, estado) 
                  VALUES (?, ?, 'activo')";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("id", $sucursal_combustible_id, $capacidad_inicial);
        return $stmt->execute();
    }

    // Crear relación entre sucursal y combustible (método alternativo)
    public function crearRelacion($sucursal_id, $combustible_id, $capacidad_inicial = 0) {
        return $this->asignarCombustible($sucursal_id, $combustible_id);
    }
    
    // Obtener combustibles por sucursal con información de almacenamiento
    public function obtenerCombustiblesPorSucursal($sucursal_id) {
        $query = "SELECT c.id, 
                         c.tipo, 
                         COALESCE(a.cap_actual, 0) as capacidad_actual, 
                         COALESCE(a.fecha, NOW()) as fecha_actualizada, 
                         COALESCE(a.estado, 'inactivo') as estado,
                         sc.id as sucursal_combustible_id,
                         a.id as almacenamiento_id
                  FROM sucursal_combustible sc
                  JOIN combustible c ON c.id = sc.combustible_id
                  LEFT JOIN almacenamiento a ON sc.id = a.sucursal_combustible_id
                  WHERE sc.sucursal_id = ?
                  ORDER BY c.tipo";
    
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Eliminar todos los combustibles de una sucursal
    public function eliminarCombustiblesDeSucursal($sucursal_id) {
        try {
            $this->db->begin_transaction();

            // Primero eliminar cola_estimada relacionada
            $query1 = "DELETE ce FROM cola_estimada ce 
                      JOIN almacenamiento a ON ce.almacenamiento_id = a.id
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      WHERE sc.sucursal_id = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("i", $sucursal_id);
            $stmt1->execute();

            // Luego eliminar almacenamiento
            $query2 = "DELETE a FROM almacenamiento a 
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      WHERE sc.sucursal_id = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("i", $sucursal_id);
            $stmt2->execute();

            // Finalmente eliminar sucursal_combustible
            $query3 = "DELETE FROM sucursal_combustible WHERE sucursal_id = ?";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->bind_param("i", $sucursal_id);
            $result = $stmt3->execute();

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en eliminarCombustiblesDeSucursal: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar un combustible específico de una sucursal
    public function eliminarCombustible($sucursal_id, $combustible_id) {
        try {
            $this->db->begin_transaction();

            // Obtener el ID de sucursal_combustible
            $query_sc = "SELECT id FROM sucursal_combustible WHERE sucursal_id = ? AND combustible_id = ?";
            $stmt_sc = $this->db->prepare($query_sc);
            $stmt_sc->bind_param("ii", $sucursal_id, $combustible_id);
            $stmt_sc->execute();
            $sc_result = $stmt_sc->get_result()->fetch_assoc();

            if (!$sc_result) {
                $this->db->rollback();
                return false;
            }

            $sucursal_combustible_id = $sc_result['id'];

            // Eliminar cola_estimada relacionada
            $query1 = "DELETE ce FROM cola_estimada ce 
                      JOIN almacenamiento a ON ce.almacenamiento_id = a.id
                      WHERE a.sucursal_combustible_id = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("i", $sucursal_combustible_id);
            $stmt1->execute();

            // Eliminar almacenamiento
            $query2 = "DELETE FROM almacenamiento WHERE sucursal_combustible_id = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("i", $sucursal_combustible_id);
            $stmt2->execute();

            // Eliminar sucursal_combustible
            $query3 = "DELETE FROM sucursal_combustible WHERE id = ?";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->bind_param("i", $sucursal_combustible_id);
            $result = $stmt3->execute();

            $this->db->commit();
            return $result;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en eliminarCombustible: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar estado del combustible en la sucursal
    public function actualizarEstadoCombustible($sucursal_id, $combustible_id, $estado) {
        try {
            // Actualizar estado en almacenamiento
            $query = "UPDATE almacenamiento a 
                     JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                     SET a.estado = ?, a.fecha = CURRENT_TIMESTAMP
                     WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sii", $estado, $sucursal_id, $combustible_id);
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en actualizarEstadoCombustible: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar tanque de combustible en la sucursal
    public function actualizarTanque($sucursal_id, $combustible_id, $capacidad_actual, $estado) {
        try {
            // Actualizar almacenamiento
            $query = "UPDATE almacenamiento a 
                     JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                     SET a.cap_actual = ?, a.estado = ?, a.fecha = CURRENT_TIMESTAMP 
                     WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("dsii", $capacidad_actual, $estado, $sucursal_id, $combustible_id);
            
            return $stmt->execute();

        } catch (Exception $e) {
            error_log("Error en actualizarTanque: " . $e->getMessage());
            return false;
        }
    }

    // Obtener información completa de tanques para una sucursal
    public function obtenerTanquesSucursal($sucursal_id) {
        $query = "SELECT c.id as combustible_id,
                         c.tipo,
                         a.cap_actual as capacidad_actual,
                         a.estado,
                         a.fecha as fecha_actualizada,
                         sc.id as sucursal_combustible_id
                  FROM sucursal_combustible sc
                  JOIN combustible c ON sc.combustible_id = c.id
                  LEFT JOIN almacenamiento a ON sc.id = a.sucursal_combustible_id
                  WHERE sc.sucursal_id = ?
                  ORDER BY c.tipo";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}