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

    public function actualizarEstimacionAutomatica($sucursal_id, $combustible_id, $capacidad_actual) {
        // Consulta para obtener los datos necesarios para el cálculo
        $query = "SELECT sc.id as sucursal_combustible_id, pc.consumo_promedio_por_auto, pc.tiempo_promedio_carga, pc.largo_promedio_auto
              FROM sucursal_combustible sc
              JOIN parametros_combustible pc ON sc.combustible_id = pc.combustible_id
              WHERE sc.sucursal_id = ? AND sc.combustible_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $sucursal_id, $combustible_id);
            $stmt->execute();
            $datos = $stmt->get_result()->fetch_assoc();

        var_dump('Datos obtenidos de la DB:', $datos);
    
        if (!$datos) return false;

            var_dump('Entradas al cálculo:',
            'Capacidad actual:' , $capacidad_actual,
            'Cconsumo_promedio_por_auto:', $datos['consumo_promedio_por_auto'],
            'tiempo_promedio_carga:', $datos['tiempo_promedio_carga'],
            'largo_promedio_auto:', $datos['largo_promedio_auto']
        );
    
        // Calcular la estimación
        $estimacion = $this->calcularEstimacion(
            $capacidad_actual,
            $datos['consumo_promedio_por_auto'],  // Consumo por auto
            $datos['tiempo_promedio_carga'],     // Tiempo por auto
            $datos['largo_promedio_auto']        // Largo del vehículo
        );
        var_dump('Resultado de la estimación:', $estimacion);
    
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
            $stmt->bind_param("iids",
                $datos['sucursal_combustible_id'],
                $estimacion['numero_de_autos'],
                $estimacion['distancia_cola'],
                $estimacion['tiempo_agotamiento']
        );
    
        // Depuración: Verifica los valores antes de ejecutar
        var_dump("DEBUG:");
        var_dump(
            $datos['sucursal_combustible_id'],
            $estimacion['numero_de_autos'],
            $estimacion['distancia_cola'],
            $estimacion['tiempo_agotamiento']
        );
    
        return $stmt->execute();
    }

    private function calcularEstimacion($capacidad_actual, $consumo_por_auto, $tiempo_por_auto, $largo_vehiculo) {
        if ($consumo_por_auto <= 0) {
            return [
                'numero_de_autos' => 0,
                'distancia_cola' => 0,
                'tiempo_agotamiento' => 0
            ];
        }
    
        // Calcular cuántos autos pueden abastecerse con la capacidad actual
        $numero_de_autos = floor($capacidad_actual / $consumo_por_auto);
        $distancia_cola = $numero_de_autos * $largo_vehiculo;
        
        // Convertir minutos a segundos
        $tiempo_segundos_por_auto = $this->convertirTiempoATotalSegundos($tiempo_por_auto);
        $tiempo_total_seg = $numero_de_autos * $tiempo_segundos_por_auto;
        
        // Formatear a HH:MM:SS
        $horas = floor($tiempo_total_seg / 3600);
        $minutos = floor(($tiempo_total_seg % 3600) / 60);
        $segundos = $tiempo_total_seg % 60;
        
        return [
            'numero_de_autos' => $numero_de_autos,
            'distancia_cola' => $distancia_cola,
            'tiempo_agotamiento' => sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos)
        ];
    }

    private function convertirTiempoATotalSegundos($tiempo) {
        list($horas, $minutos, $segundos) = explode(':', $tiempo);
        return ($horas * 3600) + ($minutos * 60) + $segundos;
    }

    public function obtenerEstimacionesSucursal($sucursal_id) {
        $query = "SELECT ce.*, c.tipo AS tipo_combustible, sc.capacidad_actual
                  FROM cola_estimada ce
                  JOIN sucursal_combustible sc ON ce.sucursal_combustible_id = sc.id
                  JOIN combustible c ON sc.combustible_id = c.id
                  WHERE sc.sucursal_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $sucursal_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    
}
?>
