<?php
require_once __DIR__ . '/../model/Malmacenamiento.php';
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';
require_once __DIR__ . '/../model/Mcola_estimada.php';

class Calmacenamiento {
    private $Malmacenamiento;
    private $Msucursal;
    private $Mcombustible;
    private $Mcola_estimada;

    public function __construct() {
        $this->Malmacenamiento = new Malmacenamiento();
        $this->Msucursal = new Msucursal();
        $this->Mcombustible = new Mcombustible();
        $this->Mcola_estimada = new Mcola_estimada();
    }

    // Mostrar dashboard principal de almacenamiento
    public function index() {
        try {
            // Obtener estadísticas generales
            $estadisticas = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento();
            
            // Obtener todas las sucursales con sus almacenamientos
            $sucursales_result = $this->Msucursal->obtenerSucursales();
            $sucursales = [];
            if ($sucursales_result && is_object($sucursales_result)) {
                while ($row = $sucursales_result->fetch_assoc()) {
                    $row['almacenamientos'] = $this->Malmacenamiento->obtenerAlmacenamientosPorSucursal($row['id']);
                    $row['estadisticas'] = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento($row['id']);
                    $sucursales[] = $row;
                }
            }

            $datos_vista = [
                'estadisticas_generales' => $estadisticas,
                'sucursales' => $sucursales
            ];

            require_once __DIR__ . '/../view/Valmacenamiento/index.php';

        } catch (Exception $e) {
            error_log("Error en index almacenamiento: " . $e->getMessage());
            $this->mostrarError("Error al cargar el dashboard de almacenamiento");
        }
    }

    // Gestionar almacenamientos de una sucursal específica
    public function gestionar() {
        try {
            $sucursal_id = $_GET['id'] ?? null;
            
            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            // Obtener datos de la sucursal
            $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
            if (!$sucursal) {
                throw new Exception("Sucursal no encontrada");
            }

            // Obtener almacenamientos de la sucursal
            $almacenamientos = $this->Malmacenamiento->obtenerAlmacenamientosPorSucursal($sucursal_id);
            
            // Obtener combustibles disponibles para asignar
            $combustibles_result = $this->Mcombustible->obtenerCombustible();
            $combustibles_disponibles = [];
            if ($combustibles_result && is_object($combustibles_result)) {
                while ($row = $combustibles_result->fetch_assoc()) {
                    // Verificar si ya está asignado a esta sucursal
                    $ya_asignado = false;
                    foreach ($almacenamientos as $alm) {
                        if ($alm['combustible_id'] == $row['id']) {
                            $ya_asignado = true;
                            break;
                        }
                    }
                    if (!$ya_asignado) {
                        $combustibles_disponibles[] = $row;
                    }
                }
            }

            $datos_vista = [
                'sucursal' => $sucursal,
                'almacenamientos' => $almacenamientos,
                'combustibles_disponibles' => $combustibles_disponibles
            ];

            require_once __DIR__ . '/../view/Valmacenamiento/gestionar.php';

        } catch (Exception $e) {
            error_log("Error en gestionar almacenamiento: " . $e->getMessage());
            $this->mostrarError($e->getMessage());
        }
    }

    // Actualizar múltiples almacenamientos
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=almacenamiento");
            exit;
        }

        try {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $almacenamientos_data = $_POST['almacenamientos'] ?? [];

            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (empty($almacenamientos_data)) {
                throw new Exception("No se recibieron datos de almacenamientos");
            }

            $errores = [];
            $actualizaciones_exitosas = 0;

            foreach ($almacenamientos_data as $almacenamiento_id => $datos) {
                try {
                    // Validar datos
                    $capacidad = floatval($datos['cap_actual'] ?? 0);
                    $capacidad_maxima = !empty($datos['cap_maxima']) ? floatval($datos['cap_maxima']) : null;
                    $estado = $datos['estado'] ?? 'inactivo';

                    if ($capacidad < 0) {
                        throw new Exception("Capacidad inválida para almacenamiento ID: $almacenamiento_id");
                    }

                    if ($capacidad_maxima !== null && $capacidad > $capacidad_maxima) {
                        throw new Exception("Capacidad actual no puede ser mayor que la máxima para almacenamiento ID: $almacenamiento_id");
                    }

                    // Actualizar almacenamiento
                    $resultado = $this->actualizarAlmacenamientoCompleto($almacenamiento_id, $capacidad, $capacidad_maxima, $estado);

                    if ($resultado) {
                        $actualizaciones_exitosas++;
                        
                        // Si está activo, recalcular estimación
                        if ($estado === 'activo') {
                            $this->recalcularEstimacion($almacenamiento_id, $capacidad);
                        }
                    } else {
                        $errores[] = "Error al actualizar almacenamiento ID: $almacenamiento_id";
                    }

                } catch (Exception $e) {
                    $errores[] = $e->getMessage();
                    error_log("Error actualizando almacenamiento $almacenamiento_id: " . $e->getMessage());
                }
            }

            // Preparar mensaje de respuesta
            session_start();
            if ($actualizaciones_exitosas > 0 && empty($errores)) {
                $_SESSION['success'] = "Almacenamientos actualizados correctamente";
            } elseif ($actualizaciones_exitosas > 0 && !empty($errores)) {
                $_SESSION['warning'] = "Algunos almacenamientos se actualizaron, pero hubo errores: " . implode(', ', $errores);
            } else {
                $_SESSION['error'] = "Error al actualizar almacenamientos: " . implode(', ', $errores);
            }

            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en actualizar almacenamientos: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al actualizar almacenamientos: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;
        }
    }

    // Actualizar un almacenamiento específico
    private function actualizarAlmacenamientoCompleto($almacenamiento_id, $capacidad, $capacidad_maxima, $estado) {
        try {
            $query = "UPDATE almacenamiento 
                      SET cap_actual = ?, cap_maxima = ?, estado = ?, fecha = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            
            $database = new Database();
            $db = $database->obtenerConexion();
            $stmt = $db->prepare($query);
            $stmt->bind_param("ddsi", $capacidad, $capacidad_maxima, $estado, $almacenamiento_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error en actualizarAlmacenamientoCompleto: " . $e->getMessage());
            return false;
        }
    }

    // Asignar nuevo combustible a sucursal
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=almacenamiento");
            exit;
        }

        try {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $combustible_id = $_POST['combustible_id'] ?? null;
            $capacidad_inicial = floatval($_POST['capacidad_inicial'] ?? 0);
            $capacidad_maxima = !empty($_POST['capacidad_maxima']) ? floatval($_POST['capacidad_maxima']) : null;

            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (!$combustible_id || !is_numeric($combustible_id)) {
                throw new Exception("ID de combustible inválido");
            }

            if ($capacidad_inicial < 0) {
                throw new Exception("Capacidad inicial inválida");
            }

            if ($capacidad_maxima !== null && $capacidad_inicial > $capacidad_maxima) {
                throw new Exception("Capacidad inicial no puede ser mayor que la máxima");
            }

            // Crear relación sucursal-combustible
            require_once __DIR__ . '/../model/Msucursal_combustible.php';
            $msucursal_combustible = new Msucursal_combustible();
            
            $resultado = $msucursal_combustible->asignarCombustible($sucursal_id, $combustible_id);
            
            if ($resultado) {
                // Actualizar el almacenamiento creado automáticamente
                $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorSucursalCombustible($sucursal_id, $combustible_id);
                
                if ($almacenamiento) {
                    $this->actualizarAlmacenamientoCompleto(
                        $almacenamiento['id'], 
                        $capacidad_inicial, 
                        $capacidad_maxima, 
                        'activo'
                    );
                    
                    // Calcular estimación inicial si está activo
                    if ($capacidad_inicial > 0) {
                        $this->recalcularEstimacion($almacenamiento['id'], $capacidad_inicial);
                    }
                }
            }

            session_start();
            if ($resultado) {
                $_SESSION['success'] = "Combustible asignado correctamente";
            } else {
                $_SESSION['error'] = "Error al asignar el combustible";
            }

            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en asignarCombustible: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al asignar combustible: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;
        }
    }

    // Eliminar almacenamiento (y relación sucursal-combustible)
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=almacenamiento");
            exit;
        }

        try {
            $almacenamiento_id = $_POST['almacenamiento_id'] ?? null;
            $sucursal_id = $_POST['sucursal_id'] ?? null;

            if (!$almacenamiento_id || !is_numeric($almacenamiento_id)) {
                throw new Exception("ID de almacenamiento inválido");
            }

            // Obtener información del almacenamiento antes de eliminar
            $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorId($almacenamiento_id);
            
            if (!$almacenamiento) {
                throw new Exception("Almacenamiento no encontrado");
            }

            // Eliminar almacenamiento (esto también eliminará sucursal_combustible por CASCADE)
            require_once __DIR__ . '/../model/Msucursal_combustible.php';
            $msucursal_combustible = new Msucursal_combustible();
            
            $resultado = $msucursal_combustible->eliminarCombustible(
                $almacenamiento['sucursal_id'], 
                $almacenamiento['combustible_id']
            );

            session_start();
            if ($resultado) {
                $_SESSION['success'] = "Almacenamiento eliminado correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar el almacenamiento";
            }

            header("Location: index.php?action=gestionar_almacenamiento&id=" . ($sucursal_id ?? $almacenamiento['sucursal_id']));
            exit;

        } catch (Exception $e) {
            error_log("Error en eliminar almacenamiento: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al eliminar almacenamiento: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;
        }
    }

    // Recalcular estimación para un almacenamiento
    private function recalcularEstimacion($almacenamiento_id, $capacidad) {
        try {
            // Obtener información del almacenamiento
            $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorId($almacenamiento_id);
            
            if ($almacenamiento && $almacenamiento['estado'] === 'activo') {
                $this->Mcola_estimada->actualizarEstimacionAutomatica(
                    $almacenamiento['sucursal_id'],
                    $almacenamiento['combustible_id'],
                    $capacidad
                );
            }
        } catch (Exception $e) {
            error_log("Error recalculando estimación: " . $e->getMessage());
        }
    }

    // Ver detalles de un almacenamiento específico
    public function detalle() {
        try {
            $almacenamiento_id = $_GET['id'] ?? null;
            
            if (!$almacenamiento_id || !is_numeric($almacenamiento_id)) {
                throw new Exception("ID de almacenamiento inválido");
            }

            // Obtener información completa del almacenamiento
            $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorId($almacenamiento_id);
            
            if (!$almacenamiento) {
                throw new Exception("Almacenamiento no encontrado");
            }

            // Obtener estimación de cola si existe
            $estimacion = [];
            if ($almacenamiento['estado'] === 'activo') {
                $estimaciones = $this->Mcola_estimada->obtenerEstimacionesSucursal($almacenamiento['sucursal_id']);
                foreach ($estimaciones as $est) {
                    if ($est['tipo_combustible'] === $almacenamiento['combustible_tipo']) {
                        $estimacion = $est;
                        break;
                    }
                }
            }

            $datos_vista = [
                'almacenamiento' => $almacenamiento,
                'estimacion' => $estimacion
            ];

            require_once __DIR__ . '/../view/Valmacenamiento/detalle.php';

        } catch (Exception $e) {
            error_log("Error en detalle almacenamiento: " . $e->getMessage());
            $this->mostrarError($e->getMessage());
        }
    }

    // Mostrar reportes de almacenamiento
    public function reportes() {
        try {
            // Obtener estadísticas generales
            $estadisticas_generales = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento();
            
            // Obtener almacenamientos activos
            $almacenamientos_activos = $this->Malmacenamiento->obtenerAlmacenamientosActivos();
            
            // Obtener estadísticas por sucursal
            $sucursales_result = $this->Msucursal->obtenerSucursales();
            $estadisticas_por_sucursal = [];
            if ($sucursales_result && is_object($sucursales_result)) {
                while ($row = $sucursales_result->fetch_assoc()) {
                    $row['estadisticas'] = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento($row['id']);
                    $estadisticas_por_sucursal[] = $row;
                }
            }

            $datos_vista = [
                'estadisticas_generales' => $estadisticas_generales,
                'almacenamientos_activos' => $almacenamientos_activos,
                'estadisticas_por_sucursal' => $estadisticas_por_sucursal
            ];

            require_once __DIR__ . '/../view/Valmacenamiento/reportes.php';

        } catch (Exception $e) {
            error_log("Error en reportes almacenamiento: " . $e->getMessage());
            $this->mostrarError("Error al cargar los reportes");
        }
    }

    // Mostrar página de error
    private function mostrarError($mensaje) {
        $error = $mensaje;
        require_once __DIR__ . '/../view/error.php';
    }
}