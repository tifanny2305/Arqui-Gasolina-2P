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
    
    public function crearAlmacenamiento($sucursal_combustible_id, $cap_actual = 0, $estado = 'activo') {
        $query = "INSERT INTO almacenamiento (sucursal_combustible_id, cap_actual, estado) 
                  VALUES (?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ids", $sucursal_combustible_id, $cap_actual, $estado);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }
    
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
    
    public function obtenerAlmacenamientosPorSucursal($sucursal_id) {
        $query = "SELECT a.*, 
                         c.tipo as combustible_tipo,
                         sc.combustible_id
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
    
    public function actualizarCapacidad($id, $nueva_capacidad) {
        $query = "UPDATE almacenamiento 
                  SET cap_actual = ?, fecha = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("di", $nueva_capacidad, $id);
        
        return $stmt->execute();
    }
    
    public function actualizarEstado($id, $nuevo_estado) {
        $query = "UPDATE almacenamiento 
                  SET estado = ?, fecha = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $nuevo_estado, $id);
        
        return $stmt->execute();
    }
    
    public function actualizarAlmacenamiento($id, $capacidad, $estado) {
        $query = "UPDATE almacenamiento 
                  SET cap_actual = ?, estado = ?, fecha = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("dsi", $capacidad, $estado, $id);
        
        return $stmt->execute();
    }
    
    public function eliminarAlmacenamiento($id) {
        $query = "DELETE FROM almacenamiento WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function obtenerAlmacenamientosActivos($sucursal_id = null) {
        if ($sucursal_id) {
            $query = "SELECT a.*, 
                             c.tipo as combustible_tipo,
                             s.nombre as sucursal_nombre
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      WHERE a.estado = 'activo' AND sc.sucursal_id = ?
                      ORDER BY s.nombre, c.tipo";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sucursal_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $query = "SELECT a.*, 
                             c.tipo as combustible_tipo,
                             s.nombre as sucursal_nombre
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      WHERE a.estado = 'activo'
                      ORDER BY s.nombre, c.tipo";
            
            $result = $this->db->query($query);
            return $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    public function existeAlmacenamiento($sucursal_combustible_id) {
        $query = "SELECT id FROM almacenamiento WHERE sucursal_combustible_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_combustible_id);
        $stmt->execute();
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['id'] : false;
    }
    
    public function obtenerEstadisticasAlmacenamiento($sucursal_id = null) {
        if ($sucursal_id) {
            $query = "SELECT 
                        COUNT(*) as total_tanques,
                        COUNT(CASE WHEN a.estado = 'activo' THEN 1 END) as tanques_activos,
                        COUNT(CASE WHEN a.estado = 'inactivo' THEN 1 END) as tanques_inactivos,
                        SUM(CASE WHEN a.estado = 'activo' THEN a.cap_actual ELSE 0 END) as capacidad_total
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      WHERE sc.sucursal_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sucursal_id);
            $stmt->execute();
            return $stmt->get_result()->fetch_assoc();
        } else {
            $query = "SELECT 
                        COUNT(*) as total_tanques,
                        COUNT(CASE WHEN estado = 'activo' THEN 1 END) as tanques_activos,
                        COUNT(CASE WHEN estado = 'inactivo' THEN 1 END) as tanques_inactivos,
                        SUM(CASE WHEN estado = 'activo' THEN cap_actual ELSE 0 END) as capacidad_total
                      FROM almacenamiento";
            
            $result = $this->db->query($query);
            return $result->fetch_assoc();
        }
    }
}
?>