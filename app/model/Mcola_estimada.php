<?php
require_once __DIR__ . '/../config/database.php';

class Mcola_estimada
{
    private $db; 

    public $id;
    public $sucursal_combustible_id;
    public $cant_autos;
    public $distancia_cola;
    public $tiempo_agotamiento;
    public $fecha_actualizada;

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    /*public function calcularEstimacion($capacidad_actual, $consumo_por_auto, $tiempo_por_auto, $largo_vehiculo) {
        // Calcular cuántos autos se pueden atender con la capacidad disponible
        $numero_de_autos = $capacidad_actual / $consumo_por_auto;
        
        // Calcular el tiempo total que tomará para que el combustible se agote
        // El tiempo de llenado total será el tiempo por auto multiplicado por el número de autos
        $tiempo_agotamiento = $numero_de_autos * $tiempo_por_auto;  // en minutos
    
        // Calcular la distancia total de la cola
        // La distancia de la fila será el número de autos multiplicado por el largo de un vehículo
        $distancia_cola = $numero_de_autos * $largo_vehiculo; // en metros
        
        // Devolver los resultados en un arreglo
        return [
            'numero_de_autos' => (int)round($numero_de_autos),  // Número estimado de autos
            'tiempo_agotamiento' => gmdate("H:i:s", $tiempo_agotamiento * 60), // Tiempo estimado en formato H:i:s
            'distancia_cola' => round($distancia_cola, 2) // Distancia estimada de la cola en metros
        ];
    }*/

    /*public function obtenerPorSucursal($sucursal_id) {
        $query = "SELECT ce.*, c.tipo, sc.capacidad_actual 
                  FROM cola_estimada ce
                  JOIN sucursal_combustible sc ON ce.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $estimaciones = [];

        while ($row = $result->fetch_assoc()) {
            $estimacion = new Mcola_estimada();
            $estimacion->id = $row['id'];
            $estimacion->sucursal_combustible_id = $row['sucursal_combustible_id'];
            $estimacion->cant_autos = $row['cant_autos'];
            $estimacion->distancia_cola = $row['distancia_cola'];
            $estimacion->tiempo_agotamiento = $row['tiempo_agotamiento'];
            $estimacion->fecha_actualizada = $row['fecha_actualizada'];
            $estimaciones[] = $estimacion;
        }

        return $estimaciones;
    }*/

    /*public function actualizarEstimacionAutomatica($sucursal_id, $combustible_id, $capacidad_actual) {
        // Consulta para obtener los datos necesarios para el cálculo
        $query = "SELECT sc.id as sucursal_combustible_id, c.consumo_por_auto, s.tiempo_por_auto, s.largo_vehiculo
                  FROM sucursal_combustible sc
                  JOIN combustible c ON sc.combustible_id = c.id
                  JOIN sucursal s ON sc.sucursal_id = s.id
                  WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();
        $datos = $stmt->get_result()->fetch_assoc();
    
        if (!$datos) return false;
    
        // Calcular la estimación
        $estimacion = $this->calcularEstimacion(
            $capacidad_actual,
            $datos['consumo_por_auto'],  // Consumo por auto
            $datos['tiempo_por_auto'],  // Tiempo por auto
            $datos['largo_vehiculo']    // Largo del vehículo
        );
    
        // Inserta o actualiza los datos en cola_estimada
        $query = "INSERT INTO cola_estimada 
                  (sucursal_combustible_id, cant_autos, distancia_cola, tiempo_agotamiento)
                  VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE
                    cant_autos = VALUES(cant_autos),
                    distancia_cola = VALUES(distancia_cola),
                    tiempo_agotamiento = VALUES(tiempo_agotamiento),
                    fecha_actualizada = CURRENT_TIMESTAMP";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iidd",
            $datos['sucursal_combustible_id'],
            $estimacion['numero_de_autos'],
            $estimacion['distancia_cola'],
            $estimacion['tiempo_agotamiento']
        );
    
        // Depuración: Verifica los valores antes de ejecutar
        var_dump(
            $datos['sucursal_combustible_id'],
            $estimacion['numero_de_autos'],
            $estimacion['distancia_cola'],
            $estimacion['tiempo_agotamiento']
        );
    
        return $stmt->execute();
    }*/

    /*public function calcularColaEstimada($sucursal_id) {
        // Consulta para obtener los datos de sucursal_combustible
        $query = "SELECT sc.id as sucursal_combustible_id, sc.combustible_id, sc.capacidad_actual
                  FROM sucursal_combustible sc
                  WHERE sc.sucursal_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        // Iterar sobre los resultados y calcular la cola estimada
        while ($row = $result->fetch_assoc()) {
            $sucursal_combustible_id = $row['sucursal_combustible_id'];
            $capacidad_actual = $row['capacidad_actual'];
    
            // Realiza los cálculos necesarios
            $cant_autos = (int)($capacidad_actual / 10); // Ejemplo: capacidad_actual dividido por 10
            $distancia_cola = $cant_autos * 5; // Ejemplo: 5 metros por auto
            $tiempo_agotamiento = $cant_autos * 2; // Ejemplo: 2 minutos por auto
    
            // Depuración: Verifica los valores antes de ejecutar
            var_dump($sucursal_combustible_id, $cant_autos, $distancia_cola, $tiempo_agotamiento);
    
            // Inserta o actualiza los datos en cola_estimada
            $insertQuery = "INSERT INTO cola_estimada (sucursal_combustible_id, cant_autos, distancia_cola, tiempo_agotamiento)
                            VALUES (?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE
                                cant_autos = VALUES(cant_autos),
                                distancia_cola = VALUES(distancia_cola),
                                tiempo_agotamiento = VALUES(tiempo_agotamiento),
                                fecha_actualizada = CURRENT_TIMESTAMP";
            $insertStmt = $this->db->prepare($insertQuery);
    
            if (!$insertStmt) {
                die("Error preparando la consulta: " . $this->db->error);
            }
    
            $insertStmt->bind_param("iidd", $sucursal_combustible_id, $cant_autos, $distancia_cola, $tiempo_agotamiento);
    
            if (!$insertStmt->execute()) {
                die("Error ejecutando la consulta: " . $insertStmt->error);
            }
        }
    }
    */

    /*public function guardarEstimacion($sucursal_combustible_id, $estimacion) {
        // Validar que exista la relación primero
        $queryCheck = "SELECT id FROM sucursal_combustible WHERE id = ?";
        $stmtCheck = $this->db->prepare($queryCheck);
        $stmtCheck->bind_param("i", $sucursal_combustible_id);
        $stmtCheck->execute();
        
        if (!$stmtCheck->get_result()->fetch_assoc()) {
            throw new Exception("No existe el tanque con ID: $sucursal_combustible_id");
        }

        // Resto del método original...
        $query = "INSERT INTO cola_estimada 
                (sucursal_combustible_id, cant_autos, distancia_cola, tiempo_agotamiento)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    cant_autos = VALUES(cant_autos),
                    distancia_cola = VALUES(distancia_cola),
                    tiempo_agotamiento = VALUES(tiempo_agotamiento),
                    fecha_actualizada = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("iids",
            $sucursal_combustible_id,
            $estimacion['numero_autos'],
            $distancia_cola,
            $estimacion['tiempo_agotamiento']
        );
        
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return false;
        }
        
        return true;
    }

    private function formatearTiempo($minutos) {
        $horas = floor($minutos / 60);
        $minutos = $minutos % 60;
        $segundos = 0;
        
        return sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
    }

    public function calcularEstimacionCombustible($datos) {
        // Validar datos requeridos
        $requeridos = ['capacidad_actual', 'consumo_por_auto', 'tiempo_por_auto', 'largo_vehiculo', 'bombas_activas'];
        foreach ($requeridos as $campo) {
            if (!isset($datos[$campo]) || $datos[$campo] === '') {
                throw new Exception("Falta el campo requerido: $campo");
            }
        }
        
        $capacidad = (float)$datos['capacidad_actual'];
        $consumo = (float)$datos['consumo_por_auto'];
        $tiempo = (float)$datos['tiempo_por_auto'];
        $largo = (float)$datos['largo_vehiculo'];
        $bombas = max(1, (int)$datos['bombas_activas']); // Al menos 1 bomba
        
        // Validar valores positivos
        if ($capacidad <= 0 || $consumo <= 0 || $tiempo <= 0 || $largo <= 0) {
            throw new Exception("Los valores deben ser mayores a cero");
        }
        
        // Cálculos
        $numero_autos = $capacidad / $consumo;
        $tiempo_total = ($numero_autos * $tiempo) / $bombas;
        $distancia_cola = $numero_autos * $largo;
        
        return [
            'numero_autos' => (int)round($numero_autos),
            'tiempo_agotamiento' => $this->formatearTiempo($tiempo_total),
            'distancia_cola' => round($distancia_cola, 2), // Redondeado a 2 decimales
            'bombas_activas' => $bombas
        ];
    }


    public function obtenerEstimacionesPorSucursal($sucursal_id) {
        $query = "SELECT 
                    ce.id,
                    ce.sucursal_combustible_id,
                    ce.cant_autos,
                    ce.distancia_cola,
                    ce.tiempo_agotamiento,
                    ce.fecha_actualizada,
                    c.tipo as tipo_combustible,
                    sc.capacidad_actual
                  FROM cola_estimada ce
                  JOIN sucursal_combustible sc ON ce.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("i", $sucursal_id);
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return false;
        }
        
        $result = $stmt->get_result();
        $estimaciones = [];
        
        while ($row = $result->fetch_assoc()) {
            $estimaciones[] = [
                'id' => $row['id'],
                'sucursal_combustible_id' => $row['sucursal_combustible_id'],
                'tipo_combustible' => $row['tipo_combustible'],
                'capacidad_actual' => $row['capacidad_actual'],
                'cant_autos' => $row['cant_autos'],
                'distancia_cola' => $row['distancia_cola'],
                'tiempo_agotamiento' => $row['tiempo_agotamiento'],
                'fecha_actualizacion' => $row['fecha_actualizada']
            ];
        }
        
        return $estimaciones;
    }

    public function obtenerDatosCombustibleSucursal($sucursal_id) {
        // Validar que el ID sea numérico y positivo
        if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
            error_log("ID de sucursal inválido: $sucursal_id");
            return false;
        }
    
        try {
            $query = "SELECT 
                        sc.id AS sucursal_combustible_id,
                        sc.sucursal_id,
                        sc.combustible_id,
                        sc.capacidad_actual,
                        c.tipo,
                        c.consumo_por_auto,
                        s.tiempo_por_auto,
                        s.largo_vehiculo,
                        s.bombas_activas,
                        s.nombre AS nombre_sucursal
                      FROM sucursal_combustible sc
                      JOIN combustible c ON sc.combustible_id = c.id
                      JOIN sucursal s ON sc.sucursal_id = s.id
                      WHERE sc.sucursal_id = ?";
            
            $stmt = $this->db->prepare($query);
            if (!$stmt) {
                throw new Exception("Error preparando la consulta: " . $this->db->error);
            }
            
            $stmt->bind_param("i", $sucursal_id);
            if (!$stmt->execute()) {
                throw new Exception("Error ejecutando la consulta: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $combustibles = [];
            
            while ($row = $result->fetch_assoc()) {
                                
                $combustibles[] = [
                    'sucursal_combustible_id' => $row['sucursal_combustible_id'],
                    'sucursal_id' => $row['sucursal_id'],
                    'combustible_id' => $row['combustible_id'],
                    'tipo' => $row['tipo'],
                    'nombre_sucursal' => $row['nombre_sucursal'],
                    'capacidad_actual' => (float)$row['capacidad_actual'],
                    'consumo_por_auto' => (float)$row['consumo_por_auto'],
                    'tiempo_por_auto' => (float)$row['tiempo_por_auto'],
                    'largo_vehiculo' => (float)$row['largo_vehiculo'],
                    'bombas_activas' => (int)$row['bombas_activas']
                ];
            }
            
            // Si no hay resultados, registrar advertencia
            if (empty($combustibles)) {
                error_log("No se encontraron combustibles para la sucursal ID: $sucursal_id");
            }
            
            return $combustibles;
            
        } catch (Exception $e) {
            error_log("Excepción en obtenerDatosCombustibleSucursal: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerTanquesSucursal($sucursal_id) {
        $query = "SELECT 
                    sc.id as sucursal_combustible_id,
                    c.tipo as combustible,
                    sc.capacidad_actual,
                    sc.fecha_actualizada,
                    sc.estado
                  FROM sucursal_combustible sc
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?";
        
        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            error_log("Error preparando consulta: " . $this->db->error);
            return false;
        }
        
        $stmt->bind_param("i", $sucursal_id);
        if (!$stmt->execute()) {
            error_log("Error ejecutando consulta: " . $stmt->error);
            return false;
        }
        
        $result = $stmt->get_result();
        $tanques = [];
        
        while ($row = $result->fetch_assoc()) {
            $tanques[] = [
                'id' => $row['sucursal_combustible_id'],
                'combustible' => $row['combustible'],
                'capacidad' => $row['capacidad_actual'],
                'fecha' => $row['fecha_actualizada'],
                'estado' => $row['estado']
            ];
        }
        
        return $tanques;
    }

    public function sincronizarRelaciones() {
        // Eliminar registros huérfanos
        $queryDelete = "DELETE ce FROM cola_estimada ce
                       LEFT JOIN sucursal_combustible sc ON ce.sucursal_combustible_id = sc.id
                       WHERE sc.id IS NULL";
        
        $this->db->query($queryDelete);
        
        // Crear registros faltantes
        $queryInsert = "INSERT INTO cola_estimada (sucursal_combustible_id, cant_autos, distancia_cola, tiempo_agotamiento)
                       SELECT sc.id, 0, 0, '00:00:00'
                       FROM sucursal_combustible sc
                       LEFT JOIN cola_estimada ce ON sc.id = ce.sucursal_combustible_id
                       WHERE ce.id IS NULL";
        
        return $this->db->query($queryInsert);
    
    }

    public function obtenerRelacionId($sucursal_id, $combustible_id) {
        $query = "SELECT id FROM sucursal_combustible 
                  WHERE sucursal_id = ? AND combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    public function obtenerDatosParaCalculo($sucursal_id, $combustible_id) {
        $query = "SELECT 
                    c.consumo_por_auto,
                    s.tiempo_por_auto,
                    s.largo_vehiculo,
                    s.bombas_activas
                  FROM sucursal_combustible sc
                  JOIN combustible c ON sc.combustible_id = c.id
                  JOIN sucursal s ON sc.sucursal_id = s.id
                  WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }*/

    public function calcularEstimacion($datos) {
        // Validaciones
        $requeridos = ['capacidad_actual', 'consumo_por_auto', 'tiempo_por_auto', 'largo_vehiculo', 'bombas_activas'];
        foreach ($requeridos as $campo) {
            if (!isset($datos[$campo])) {
                throw new Exception("Campo requerido faltante: $campo");
            }
        }

        $capacidad = (float)$datos['capacidad_actual'];
        $consumo = (float)$datos['consumo_por_auto'];
        $tiempo = (float)$datos['tiempo_por_auto'];
        $largo = (float)$datos['largo_vehiculo'];
        $bombas = max(1, (int)$datos['bombas_activas']);

        if ($capacidad <= 0 || $consumo <= 0 || $tiempo <= 0 || $largo <= 0) {
            throw new Exception("Todos los valores deben ser positivos");
        }

        // Cálculos
        $numero_autos = $capacidad / $consumo;
        $tiempo_total = ($numero_autos * $tiempo) / $bombas;
        $distancia_cola = $numero_autos * $largo;

        return [
            'numero_autos' => (int)round($numero_autos),
            'tiempo_agotamiento' => $this->formatearTiempo($tiempo_total),
            'distancia_cola' => round($distancia_cola, 2),
            'bombas_activas' => $bombas
        ];
    }

    public function obtenerCombustiblesActivos($sucursal_id) {
        $query = "SELECT 
                    sc.id AS sucursal_combustible_id,
                    sc.sucursal_id,
                    sc.combustible_id,
                    sc.capacidad_actual,
                    c.tipo,
                    c.consumo_por_auto,
                    s.tiempo_por_auto,
                    s.largo_vehiculo,
                    s.bombas_activas
                  FROM sucursal_combustible sc
                  JOIN combustible c ON sc.combustible_id = c.id
                  JOIN sucursal s ON sc.sucursal_id = s.id
                  WHERE sc.sucursal_id = ? 
                  AND sc.estado = 'activo'
                  AND sc.capacidad_actual > 0";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $combustibles = [];
        
        while ($row = $result->fetch_assoc()) {
            $combustibles[] = $row;
        }
        
        return $combustibles;
    }

    public function guardarEstimacion($sucursal_combustible_id, $estimacion) {
        $query = "INSERT INTO cola_estimada 
                 (sucursal_combustible_id, cant_autos, distancia_cola, tiempo_agotamiento)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    cant_autos = VALUES(cant_autos),
                    distancia_cola = VALUES(distancia_cola),
                    tiempo_agotamiento = VALUES(tiempo_agotamiento),
                    fecha_actualizada = CURRENT_TIMESTAMP";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iids",
            $sucursal_combustible_id,
            $estimacion['numero_autos'],
            $estimacion['distancia_cola'],
            $estimacion['tiempo_agotamiento']
        );
        
        return $stmt->execute();
    }

    public function obtenerEstimaciones($sucursal_id) {
        $query = "SELECT 
                    ce.*, 
                    c.tipo as tipo_combustible,
                    sc.capacidad_actual
                  FROM cola_estimada ce
                  JOIN sucursal_combustible sc ON ce.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $estimaciones = [];
        
        while ($row = $result->fetch_assoc()) {
            $estimaciones[] = $row;
        }
        
        return $estimaciones;
    }

    /**
     * Formatea minutos a formato HH:MM:SS
     */
    private function formatearTiempo($minutos) {
        $horas = floor($minutos / 60);
        $minutos = $minutos % 60;
        return sprintf("%02d:%02d:00", $horas, $minutos);
    }
}
?>
