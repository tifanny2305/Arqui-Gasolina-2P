<?php
require_once __DIR__ . '/../config/database.php';

class Mcola_estimada
{
    private $db; 

    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Actualizar estimación automática basada en los parámetros del combustible
    public function actualizarEstimacionAutomatica($sucursal_id, $combustible_id, $capacidad_actual) {
        try {
            // Obtener datos necesarios para el cálculo
            $query = "SELECT sc.id as sucursal_combustible_id, 
                            pc.consumo_promedio_por_auto, 
                            pc.tiempo_promedio_carga, 
                            pc.largo_promedio_auto,
                            a.id as almacenamiento_id
                      FROM sucursal_combustible sc
                      LEFT JOIN parametros_combustible pc ON sc.combustible_id = pc.combustible_id
                      LEFT JOIN almacenamiento a ON sc.id = a.sucursal_combustible_id AND a.estado = 'activo'
                      WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $sucursal_id, $combustible_id);
            $stmt->execute();
            $datos = $stmt->get_result()->fetch_assoc();

            if (!$datos || !$datos['consumo_promedio_por_auto']) {
                error_log("No se encontraron parámetros para sucursal_id: $sucursal_id, combustible_id: $combustible_id");
                return false;
            }

            // Calcular la estimación
            $estimacion = $this->calcularEstimacion(
                $capacidad_actual,
                $datos['consumo_promedio_por_auto'],
                $datos['tiempo_promedio_carga'],
                $datos['largo_promedio_auto']
            );

            // Si no existe almacenamiento, crearlo
            if (!$datos['almacenamiento_id']) {
                $almacenamiento_id = $this->crearAlmacenamiento($datos['sucursal_combustible_id'], $capacidad_actual);
            } else {
                $almacenamiento_id = $datos['almacenamiento_id'];
                // Actualizar capacidad actual en almacenamiento
                $this->actualizarCapacidadAlmacenamiento($almacenamiento_id, $capacidad_actual);
            }

            // Insertar o actualizar cola_estimada
            $query_cola = "INSERT INTO cola_estimada 
                          (almacenamiento_id, cant_autos, distancia_cola, tiempo_agotamiento)
                          VALUES (?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE
                            cant_autos = VALUES(cant_autos),
                            distancia_cola = VALUES(distancia_cola),
                            tiempo_agotamiento = VALUES(tiempo_agotamiento),
                            fecha_actualizada = CURRENT_TIMESTAMP";
            
            $stmt_cola = $this->db->prepare($query_cola);
            $stmt_cola->bind_param("iids",
                $almacenamiento_id,
                $estimacion['numero_de_autos'],
                $estimacion['distancia_cola'],
                $estimacion['tiempo_agotamiento']
            );

            return $stmt_cola->execute();

        } catch (Exception $e) {
            error_log("Error en actualizarEstimacionAutomatica: " . $e->getMessage());
            return false;
        }
    }

    // Crear registro de almacenamiento
    private function crearAlmacenamiento($sucursal_combustible_id, $capacidad_actual) {
        $query = "INSERT INTO almacenamiento (sucursal_combustible_id, cap_actual, estado) 
                  VALUES (?, ?, 'activo')";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("id", $sucursal_combustible_id, $capacidad_actual);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Actualizar capacidad en almacenamiento
    private function actualizarCapacidadAlmacenamiento($almacenamiento_id, $capacidad_actual) {
        $query = "UPDATE almacenamiento SET cap_actual = ?, fecha = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("di", $capacidad_actual, $almacenamiento_id);
        return $stmt->execute();
    }

    // Calcular estimación de cola
    private function calcularEstimacion($capacidad_actual, $consumo_por_auto, $tiempo_por_auto, $largo_vehiculo) {
        if ($consumo_por_auto <= 0 || $capacidad_actual <= 0) {
            return [
                'numero_de_autos' => 0,
                'distancia_cola' => 0.00,
                'tiempo_agotamiento' => '00:00:00'
            ];
        }

        // Calcular número de autos que pueden abastecerse
        $numero_de_autos = floor($capacidad_actual / $consumo_por_auto);
        $distancia_cola = $numero_de_autos * $largo_vehiculo;
        
        // Convertir tiempo a segundos totales
        $tiempo_segundos_por_auto = $this->convertirTiempoATotalSegundos($tiempo_por_auto);
        $tiempo_total_seg = $numero_de_autos * $tiempo_segundos_por_auto;
        
        // Formatear a HH:MM:SS
        $horas = floor($tiempo_total_seg / 3600);
        $minutos = floor(($tiempo_total_seg % 3600) / 60);
        $segundos = $tiempo_total_seg % 60;
        
        return [
            'numero_de_autos' => $numero_de_autos,
            'distancia_cola' => number_format($distancia_cola, 2),
            'tiempo_agotamiento' => sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos)
        ];
    }

    // Convertir tiempo TIME a segundos totales
    private function convertirTiempoATotalSegundos($tiempo) {
        if (empty($tiempo)) return 0;
        
        $partes = explode(':', $tiempo);
        $horas = isset($partes[0]) ? intval($partes[0]) : 0;
        $minutos = isset($partes[1]) ? intval($partes[1]) : 0;
        $segundos = isset($partes[2]) ? intval($partes[2]) : 0;
        
        return ($horas * 3600) + ($minutos * 60) + $segundos;
    }

    // Obtener estimaciones para una sucursal
    public function obtenerEstimacionesSucursal($sucursal_id) {
        $query = "SELECT ce.*, 
                         c.tipo AS tipo_combustible, 
                         a.cap_actual as capacidad_actual,
                         s.nombre as sucursal_nombre
                  FROM cola_estimada ce
                  JOIN almacenamiento a ON ce.almacenamiento_id = a.id
                  JOIN sucursal_combustible sc ON a.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  JOIN sucursal s ON sc.sucursal_id = s.id
                  WHERE sc.sucursal_id = ? AND a.estado = 'activo'
                  ORDER BY c.tipo";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Actualizar todas las estimaciones de una sucursal
    public function actualizarEstimacionesSucursal($sucursal_id) {
        try {
            // Obtener todos los combustibles activos de la sucursal
            $query = "SELECT sc.combustible_id, a.cap_actual
                      FROM sucursal_combustible sc
                      JOIN almacenamiento a ON sc.id = a.sucursal_combustible_id
                      WHERE sc.sucursal_id = ? AND a.estado = 'activo'";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $sucursal_id);
            $stmt->execute();
            $combustibles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $success = true;
            foreach ($combustibles as $combustible) {
                $result = $this->actualizarEstimacionAutomatica(
                    $sucursal_id,
                    $combustible['combustible_id'],
                    $combustible['cap_actual']
                );
                if (!$result) {
                    $success = false;
                }
            }
            
            return $success;
        } catch (Exception $e) {
            error_log("Error en actualizarEstimacionesSucursal: " . $e->getMessage());
            return false;
        }
    }
}