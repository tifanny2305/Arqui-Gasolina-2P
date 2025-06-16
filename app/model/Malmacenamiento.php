<?php
require_once(__DIR__ . '/../config/database.php');

class Malmacenamiento
{
    private $db;

    // Atributos según la tabla almacenamiento
    private $id;
    private $sucursal_combustible_id;
    private $cap_actual;
    private $estado;
    private $fecha;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Crear nuevo almacenamiento
    public function crearAlmacenamiento($sucursal_combustible_id, $cap_actual = 0, $estado = 'activo')
    {
        try {
            $query = "INSERT INTO almacenamiento (sucursal_combustible_id, cap_actual, estado) 
                      VALUES (?, ?, ?)";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("ids", $sucursal_combustible_id, $cap_actual, $estado);

            if ($stmt->execute()) {
                return $this->db->insert_id;
            } else {
                throw new Exception("Error al ejecutar consulta: " . $stmt->error);
            }
        } catch (Exception $e) {
            error_log("Error en crearAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }

    // Obtener almacenamiento por ID
    public function obtenerAlmacenamientoPorId($id)
    {
        try {
            $query = "SELECT a.*, 
                             s.nombre as sucursal_nombre,
                             s.ubicacion as sucursal_ubicacion,
                             c.tipo as combustible_tipo,
                             sc.sucursal_id,
                             sc.combustible_id
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      WHERE a.id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("i", $id);
            $stmt->execute();

            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en obtenerAlmacenamientoPorId: " . $e->getMessage());
            return false;
        }
    }

    // Obtener almacenamiento por sucursal y combustible
    public function obtenerAlmacenamientoPorSucursalCombustible($sucursal_id, $combustible_id)
    {
        try {
            $query = "SELECT a.*
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("ii", $sucursal_id, $combustible_id);
            $stmt->execute();

            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Error en obtenerAlmacenamientoPorSucursalCombustible: " . $e->getMessage());
            return false;
        }
    }

    // Obtener todos los almacenamientos de una sucursal
    public function obtenerAlmacenamientosPorSucursal($sucursal_id)
    {
        try {
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
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("i", $sucursal_id);
            $stmt->execute();

            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerAlmacenamientosPorSucursal: " . $e->getMessage());
            return [];
        }
    }

    // Actualizar capacidad actual del almacenamiento
    public function actualizarCapacidad($id, $nueva_capacidad)
    {
        try {
            $query = "UPDATE almacenamiento 
                      SET cap_actual = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("di", $nueva_capacidad, $id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarCapacidad: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar estado del almacenamiento
    public function actualizarEstado($id, $nuevo_estado)
    {
        try {
            // Validar estado
            $estados_validos = ['activo', 'inactivo'];
            if (!in_array($nuevo_estado, $estados_validos)) {
                throw new Exception("Estado inválido: $nuevo_estado");
            }

            $query = "UPDATE almacenamiento 
                      SET estado = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("si", $nuevo_estado, $id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarEstado: " . $e->getMessage());
            return false;
        }
    }

    // Actualizar capacidad y estado
    public function actualizarAlmacenamiento($id, $capacidad, $estado)
    {
        try {
            // Validar estado
            $estados_validos = ['activo', 'inactivo'];
            if (!in_array($estado, $estados_validos)) {
                throw new Exception("Estado inválido: $estado");
            }

            $query = "UPDATE almacenamiento 
                      SET cap_actual = ?, estado = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";

            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("dsi", $capacidad, $estado, $id);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }

    // Eliminar almacenamiento
    public function eliminarAlmacenamiento($id)
    {
        try {
            // No necesitamos transacción manual porque ON DELETE CASCADE se encarga
            $query = "DELETE FROM almacenamiento WHERE id = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en eliminarAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }

    // Obtener almacenamientos activos
    public function obtenerAlmacenamientosActivos($sucursal_id = null)
    {
        try {
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
                if (!$stmt) {
                    throw new Exception("Error al preparar consulta: " . $this->db->error);
                }

                $stmt->bind_param("i", $sucursal_id);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

                $result = $this->db->query($query);
                if (!$result) {
                    throw new Exception("Error en la consulta: " . $this->db->error);
                }

                return $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error en obtenerAlmacenamientosActivos: " . $e->getMessage());
            return [];
        }
    }

    // Verificar si existe almacenamiento para sucursal-combustible
    public function existeAlmacenamiento($sucursal_combustible_id)
    {
        try {
            $query = "SELECT id FROM almacenamiento WHERE sucursal_combustible_id = ?";
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->db->error);
            }

            $stmt->bind_param("i", $sucursal_combustible_id);
            $stmt->execute();

            $result = $stmt->get_result()->fetch_assoc();
            return $result ? $result['id'] : false;
        } catch (Exception $e) {
            error_log("Error en existeAlmacenamiento: " . $e->getMessage());
            return false;
        }
    }

    // Obtener estadísticas de almacenamiento
    public function obtenerEstadisticasAlmacenamiento($sucursal_id = null)
    {
        try {
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
                if (!$stmt) {
                    throw new Exception("Error al preparar consulta: " . $this->db->error);
                }

                $stmt->bind_param("i", $sucursal_id);
                $stmt->execute();
                return $stmt->get_result()->fetch_assoc();
            } else {
                $query = "SELECT 
                            COUNT(*) as total_tanques,
                            COUNT(CASE WHEN estado = 'activo' THEN 1 END) as tanques_activos,
                            COUNT(CASE WHEN estado = 'inactivo' THEN 1 END) as tanques_inactivos,
                            AVG(CASE WHEN estado = 'activo' THEN cap_actual END) as capacidad_promedio,
                            SUM(CASE WHEN estado = 'activo' THEN cap_actual ELSE 0 END) as capacidad_total
                          FROM almacenamiento";

                $result = $this->db->query($query);
                if (!$result) {
                    throw new Exception("Error en la consulta: " . $this->db->error);
                }

                return $result->fetch_assoc();
            }
        } catch (Exception $e) {
            error_log("Error en obtenerEstadisticasAlmacenamiento: " . $e->getMessage());
            return [];
        }
    }

    // Obtener almacenamientos con información completa
    public function obtenerAlmacenamientosCompletos()
    {
        try {
            $query = "SELECT a.*, 
                             s.nombre as sucursal_nombre,
                             s.ubicacion as sucursal_ubicacion,
                             s.bombas as sucursal_bombas,
                             c.tipo as combustible_tipo,
                             sc.sucursal_id,
                             sc.combustible_id,
                             ce.cant_autos,
                             ce.distancia_cola,
                             ce.tiempo_agotamiento,
                             ce.fecha_actualizada as estimacion_actualizada
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id
                      LEFT JOIN cola_estimada ce ON a.id = ce.almacenamiento_id
                      ORDER BY s.nombre, c.tipo";

            $result = $this->db->query($query);
            if (!$result) {
                throw new Exception("Error en la consulta: " . $this->db->error);
            }

            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error en obtenerAlmacenamientosCompletos: " . $e->getMessage());
            return [];
        }
    }

    // Buscar almacenamientos por criterios
    public function buscarAlmacenamientos($criterios = [])
    {
        try {
            $where_conditions = [];
            $params = [];
            $types = '';

            $query = "SELECT a.*, 
                             s.nombre as sucursal_nombre,
                             c.tipo as combustible_tipo,
                             sc.sucursal_id,
                             sc.combustible_id
                      FROM almacenamiento a
                      JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      JOIN combustible c ON sc.combustible_id = c.id";

            if (!empty($criterios['estado'])) {
                $where_conditions[] = "a.estado = ?";
                $params[] = $criterios['estado'];
                $types .= 's';
            }

            if (!empty($criterios['sucursal_id'])) {
                $where_conditions[] = "sc.sucursal_id = ?";
                $params[] = $criterios['sucursal_id'];
                $types .= 'i';
            }

            if (!empty($criterios['combustible_id'])) {
                $where_conditions[] = "sc.combustible_id = ?";
                $params[] = $criterios['combustible_id'];
                $types .= 'i';
            }

            if (!empty($criterios['capacidad_minima'])) {
                $where_conditions[] = "a.cap_actual >= ?";
                $params[] = $criterios['capacidad_minima'];
                $types .= 'd';
            }

            if (!empty($where_conditions)) {
                $query .= " WHERE " . implode(" AND ", $where_conditions);
            }

            $query .= " ORDER BY s.nombre, c.tipo";

            if (!empty($params)) {
                $stmt = $this->db->prepare($query);
                if (!$stmt) {
                    throw new Exception("Error al preparar consulta: " . $this->db->error);
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            } else {
                $result = $this->db->query($query);
                if (!$result) {
                    throw new Exception("Error en la consulta: " . $this->db->error);
                }
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            error_log("Error en buscarAlmacenamientos: " . $e->getMessage());
            return [];
        }
    }
}
