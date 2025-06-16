<?php
require_once(__DIR__ . '/../config/database.php');

class Malmacenamiento {
    private $db;
    
    private $id;
    private $sucursal_combustible_id;
    private $cap_actual;
    private $estado;
    private $fecha;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }
    
    // Crear nuevo almacenamiento
    public function crearAlmacenamiento($sucursal_combustible_id, $capacidad_inicial = 0, $estado = 'activo') {
        try {
            $query = "INSERT INTO almacenamiento (sucursal_combustible_id, cap_actual, estado) 
                      VALUES (?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ids", $sucursal_combustible_id, $capacidad_inicial, $estado);
            
            if ($stmt->execute()) {
                return $this->db->insert_id;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error en crearAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener almacenamiento por ID
    public function obtenerAlmacenamientoPorId($id) {
        $query = "SELECT a.*, 
                         s.nombre as sucursal_nombre,
                         c.tipo as combustible_tipo,
                         sc.sucursal_id,
                         sc.combustible_id
                  FROM almacenamiento a
                  JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                  JOIN sucursal s ON sc.sucursal_id = s.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE a.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Obtener almacenamiento por sucursal y combustible
    public function obtenerAlmacenamientoPorSucursalCombustible($sucursal_id, $combustible_id) {
        $query = "SELECT a.*
                  FROM almacenamiento a
                  JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                  WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // Obtener todos los almacenamientos de una sucursal
    public function obtenerAlmacenamientosPorSucursal($sucursal_id) {
        $query = "SELECT a.*, 
                         c.tipo as combustible_tipo,
                         sc.combustible_id,
                         sc.sucursal_id
                  FROM almacenamiento a
                  JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?
                  ORDER BY c.tipo";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Actualizar capacidad del almacenamiento
    public function actualizarCapacidad($id, $nueva_capacidad) {
        try {
            $query = "UPDATE almacenamiento 
                      SET cap_actual = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("di", $nueva_capacidad, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarCapacidad: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar estado del almacenamiento
    public function actualizarEstado($id, $nuevo_estado) {
        try {
            $estado_valido = in_array($nuevo_estado, ['activo', 'inactivo']);
            if (!$estado_valido) {
                throw new Exception("Estado inválido");
            }
            
            $query = "UPDATE almacenamiento 
                      SET estado = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("si", $nuevo_estado, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarEstado: " . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar capacidad y estado
    public function actualizarAlmacenamiento($id, $capacidad, $estado) {
        try {
            $estado_valido = in_array($estado, ['activo', 'inactivo']);
            if (!$estado_valido) {
                throw new Exception("Estado inválido");
            }
            
            $query = "UPDATE almacenamiento 
                      SET cap_actual = ?, estado = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("dsi", $capacidad, $estado, $id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }
    
    // Eliminar almacenamiento
    public function eliminarAlmacenamiento($id) {
        try {
            $this->db->begin_transaction();
            
            // Primero eliminar cola_estimada relacionada
            $query1 = "DELETE FROM cola_estimada WHERE almacenamiento_id = ?";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->bind_param("i", $id);
            $stmt1->execute();
            
            // Luego eliminar almacenamiento
            $query2 = "DELETE FROM almacenamiento WHERE id = ?";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->bind_param("i", $id);
            $result = $stmt2->execute();
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error en eliminarAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener almacenamientos activos
    public function obtenerAlmacenamientosActivos($sucursal_id = null) {
        if ($sucursal_id) {
            $query = "SELECT a.*, 
                             c.tipo as combustible_tipo,
                             s.nombre as sucursal_nombre,
                             sc.sucursal_id,
                             sc.combustible_id
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      WHERE a.estado = 'activo' AND sc.sucursal_id = ?
                      ORDER BY s.nombre, c.tipo";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sucursal_id);
        } else {
            $query = "SELECT a.*, 
                             c.tipo as combustible_tipo,
                             s.nombre as sucursal_nombre,
                             sc.sucursal_id,
                             sc.combustible_id
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      WHERE a.estado = 'activo'
                      ORDER BY s.nombre, c.tipo";
            
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Verificar si existe almacenamiento para sucursal-combustible
    public function existeAlmacenamiento($sucursal_combustible_id) {
        $query = "SELECT id FROM almacenamiento WHERE sucursal_combustible_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_combustible_id);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : false;
    }
    
    // Obtener estadísticas de almacenamiento
    public function obtenerEstadisticasAlmacenamiento($sucursal_id = null) {
        if ($sucursal_id) {
            $query = "SELECT 
                        COUNT(*) as total_tanques,
                        COUNT(CASE WHEN a.estado = 'activo' THEN 1 END) as tanques_activos,
                        COUNT(CASE WHEN a.estado = 'inactivo' THEN 1 END) as tanques_inactivos,
                        AVG(CASE WHEN a.estado = 'activo' THEN a.cap_actual END) as capacidad_promedio,
                        SUM(CASE WHEN a.estado = 'activo' THEN a.cap_actual ELSE 0 END) as capacidad_total
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      WHERE sc.sucursal_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sucursal_id);
        } else {
            $query = "SELECT 
                        COUNT(*) as total_tanques,
                        COUNT(CASE WHEN estado = 'activo' THEN 1 END) as tanques_activos,
                        COUNT(CASE WHEN estado = 'inactivo' THEN 1 END) as tanques_inactivos,
                        AVG(CASE WHEN estado = 'activo' THEN cap_actual END) as capacidad_promedio,
                        SUM(CASE WHEN estado = 'activo' THEN cap_actual ELSE 0 END) as capacidad_total
                      FROM almacenamiento";
            
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}