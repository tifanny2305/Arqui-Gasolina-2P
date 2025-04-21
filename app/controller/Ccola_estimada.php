<?php
require_once __DIR__ . '/../model/Mcola_estimada.php';
require_once __DIR__ . '/../model/Msucursal.php';

class Ccola_estimada {
    private $Mcola_estimada;
    private $Msucursal;

    public function __construct() {
        $this->Mcola_estimada = new Mcola_estimada();
        $this->Msucursal = new Msucursal();
    }

    public function listarSucursales() {
        $sucursales = $this->Msucursal->obtenerSucursales();

        foreach ($sucursales as $sucursal) {
            $sucursal['estimaciones'] = $this->Mcola_estimada->obtenerEstimaciones($sucursal['id']);
        }

        require_once __DIR__ . '/../view/Vcola_estimada/index.php';
    }

    /*public function mostrarPorSucursal($sucursal_id) {
        // Validación
        if (!is_numeric($sucursal_id)) {
            die("ID de sucursal inválido");
        }

        // Obtener datos de la sucursal
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        if (!$sucursal) {
            die("Sucursal no encontrada");
        }

        // Obtener estimaciones precalculadas
        $estimaciones = $this->Mcola_estimada->obtenerPorSucursal($sucursal_id);
        
        // Mostrar vista
        require_once __DIR__ . '/../view/Vcola_estimada/estimacion.php';
    }*/

    /*private function calcularEstimacion($litros, $tipo, $bombas) {
        $parametros = [
            'gasolina' => [
                'consumo_por_auto' => 35,
                'metros_por_auto' => 4.5,
                'tiempo_por_auto' => 5
            ],
            'diesel' => [
                'consumo_por_auto' => 90,
                'metros_por_auto' => 8.0,
                'tiempo_por_auto' => 8
            ]
        ];

        $p = $parametros[strtolower($tipo)] ?? $parametros['gasolina'];

        // Cálculos
        $cant_autos = $litros / $p['consumo_por_auto'];
        $minutos_totales = ($cant_autos * $p['tiempo_por_auto']) / max(1, $bombas);
        
        return [
            'litros_disponibles' => $litros,
            'cant_autos' => (int)round($cant_autos),
            'distancia_cola' => round($cant_autos * $p['metros_por_auto'], 2),
            'tiempo_agotamiento' => gmdate("H:i:s", $minutos_totales * 60),
            'bombas_activas' => $bombas
        ];
    }*/

    /*public function actualizarEstimacion()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = isset($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : 0;
            $combustible_id = isset($_POST['combustible_id']) ? intval($_POST['combustible_id']) : 0;
            $capacidad_actual = isset($_POST['capacidad_actual']) ? floatval($_POST['capacidad_actual']) : 0.0;

            if ($sucursal_id > 0 && $combustible_id > 0 && $capacidad_actual > 0) {
                $modelo = new Mcola_estimada();
                $resultado = $modelo->actualizarEstimacionAutomatica($sucursal_id, $combustible_id, $capacidad_actual);

                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Estimación actualizada correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la estimación']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }*/

    /*public function listarSucursales() {

        $sucursales = $this->Msucursal->obtenerSucursales();
        
        // Crear un nuevo array con las estimaciones
        $sucursalesConEstimaciones = [];
        foreach ($sucursales as $sucursal) {
            $sucursal['estimaciones'] = $this->Mcola_estimada->obtenerEstimacionesPorSucursal($sucursal['id']);
            $sucursalesConEstimaciones[] = $sucursal;
        }
        
        // Cargar la vista con el nuevo array
        require_once __DIR__ . '/../view/Vcola_estimada/index.php';
        
    }*/

    /**
     * Muestra las estimaciones para una sucursal específica
     */
    /*public function mostrarPorSucursal($sucursal_id) {
      
        if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
            throw new Exception("ID de sucursal inválido");
        }
        
        // Obtener datos básicos de la sucursal
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Sucursal no encontrada");
        }
        
        // Obtener tanques de combustible
        $tanques = $this->Mcola_estimada->obtenerTanquesSucursal($sucursal_id);
        
        // Obtener estimaciones actuales
        $estimaciones = $this->Mcola_estimada->obtenerEstimacionesPorSucursal($sucursal_id);
        
        // Combinar datos para la vista
        $datos_vista = [
            'sucursal' => $sucursal,
            'tanques' => $tanques,
            'estimaciones' => $estimaciones,
            'error' => null
        ];
        
        // Cargar vista
        require_once __DIR__ . '/../view/Vcola_estimada/estimacion.php';
        
    }*/

    /**
     * Actualiza las estimaciones para una sucursal (endpoint API)
     */
    /*public function actualizarEstimacion() {
        header('Content-Type: application/json');
        
        try {
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            // Obtener y validar parámetros
            $sucursal_id = isset($_POST['sucursal_id']) ? intval($_POST['sucursal_id']) : 0;
            if ($sucursal_id <= 0) {
                throw new Exception('ID de sucursal inválido', 400);
            }

            // Actualizar todas las estimaciones para la sucursal
            $resultados = $this->actualizarEstimacionesSucursal($sucursal_id);
            
            if (isset($resultados['error'])) {
                throw new Exception($resultados['error'], 500);
            }

            // Respuesta exitosa
            echo json_encode([
                'success' => true,
                'message' => 'Estimaciones actualizadas correctamente',
                'data' => $resultados
            ]);
            
        } catch (Exception $e) {
            // Manejo de errores
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage(),
                'sucursal_id' => $sucursal_id ?? 0
            ]);
        }
    }*/

    /**
     * Método interno para actualizar todas las estimaciones de una sucursal
     */
    /*private function actualizarEstimacionesSucursal($sucursal_id) {
        try {
            // 1. Obtener datos de combustibles de la sucursal
            $combustibles = $this->Mcola_estimada->obtenerDatosCombustibleSucursal($sucursal_id);
            
            if (empty($combustibles)) {
                throw new Exception("La sucursal no tiene combustibles registrados");
            }
            
            $resultados = [];
            
            // 2. Procesar cada combustible
            foreach ($combustibles as $combustible) {
                // 3. Calcular estimación
                $estimacion = $this->Mcola_estimada->calcularEstimacionCombustible($combustible);
                
                // 4. Guardar en BD
                $guardado = $this->Mcola_estimada->guardarEstimacion(
                    $combustible['sucursal_combustible_id'],
                    $estimacion
                );
                
                if (!$guardado) {
                    throw new Exception("Error guardando estimación para combustible ID: " . $combustible['combustible_id']);
                }
                
                $resultados[] = [
                    'combustible_id' => $combustible['combustible_id'],
                    'tipo' => $combustible['tipo'],
                    'estimacion' => $estimacion,
                    'status' => 'success'
                ];
            }
            
            return $resultados;
            
        } catch (Exception $e) {
            error_log("Error en actualizarEstimacionesSucursal: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'sucursal_id' => $sucursal_id
            ];
        }
    }

    public function actualizarTanque($sucursal_id, $combustible_id, $capacidad) {
        // 1. Actualizar el tanque en sucursal_combustible
        $actualizado = $this->Msucursal->actualizarCapacidadTanque(
            $sucursal_id, 
            $combustible_id, 
            $capacidad
        );
        
        if (!$actualizado) {
            throw new Exception("No se pudo actualizar el tanque");
        }
        
        // 2. Obtener el ID de la relación
        $relacion = $this->Mcola_estimada->obtenerRelacionId($sucursal_id, $combustible_id);
        
        if (!$relacion) {
            throw new Exception("No se encontró la relación sucursal-combustible");
        }
        
        // 3. Calcular y guardar nueva estimación
        $datosCalculo = $this->Mcola_estimada->obtenerDatosParaCalculo(
            $sucursal_id,
            $combustible_id
        );
        
        $estimacion = $this->Mcola_estimada->calcularEstimacionCombustible([
            'capacidad_actual' => $capacidad,
            'consumo_por_auto' => $datosCalculo['consumo_por_auto'],
            'tiempo_por_auto' => $datosCalculo['tiempo_por_auto'],
            'largo_vehiculo' => $datosCalculo['largo_vehiculo'],
            'bombas_activas' => $datosCalculo['bombas_activas']
        ]);
        
        return $this->Mcola_estimada->guardarEstimacion(
            $relacion['id'],
            $estimacion
        );
    }*/

    public function mostrarSucursal($sucursal_id) {
        if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
            throw new Exception("ID de sucursal inválido");
        }
        
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Sucursal no encontrada");
        }
        
        $estimaciones = $this->Mcola_estimada->obtenerEstimaciones($sucursal_id);
        $tanques = $this->Msucursal->obtenerTanquesSucursal($sucursal_id);
        
        require_once __DIR__ . '/../view/Vcola_estimada/estimacion.php';
    }

    public function actualizarEstimaciones($sucursal_id) {
        header('Content-Type: application/json');
    
        // Validación del parámetro
        if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'ID de sucursal inválido'
            ]);
            return;
        }
    
        try {
            // Obtener combustibles activos en la sucursal
            $combustibles = $this->Mcola_estimada->obtenerCombustiblesActivos($sucursal_id);
    
            if (empty($combustibles)) {
                throw new Exception("La sucursal no tiene combustibles activos con capacidad");
            }
    
            $resultados = [];
    
            foreach ($combustibles as $combustible) {
                // Calcular estimación basada en los datos del combustible
                $estimacion = $this->Mcola_estimada->calcularEstimacion([
                    'capacidad_actual'  => $combustible['capacidad_actual'],
                    'consumo_por_auto'  => $combustible['consumo_por_auto'],
                    'tiempo_por_auto'   => $combustible['tiempo_por_auto'],
                    'largo_vehiculo'    => $combustible['largo_vehiculo'],
                    'bombas_activas'    => $combustible['bombas_activas']
                ]);
    
                // Guardar la estimación en la base de datos
                $this->Mcola_estimada->guardarEstimacion(
                    $combustible['sucursal_combustible_id'],
                    $estimacion
                );
    
                // Agregar al arreglo de resultados
                $resultados[] = [
                    'combustible_id' => $combustible['combustible_id'],
                    'tipo'           => $combustible['tipo'],
                    'estimacion'     => $estimacion
                ];
            }
    
            // Éxito
            echo json_encode([
                'success' => true,
                'message' => 'Estimaciones actualizadas correctamente',
                'data'    => $resultados
            ]);
    
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar las estimaciones: ' . $e->getMessage()
            ]);
        }
    }
    
}
?>