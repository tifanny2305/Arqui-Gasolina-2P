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
            
            $datos_vista = [
                'sucursal' => $sucursal,
                'almacenamientos' => $almacenamientos
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
                    $estado = $datos['estado'] ?? 'inactivo';

                    if ($capacidad < 0) {
                        throw new Exception("Capacidad inválida para almacenamiento ID: $almacenamiento_id");
                    }

                    // Actualizar almacenamiento
                    $resultado = $this->Malmacenamiento->actualizarAlmacenamiento($almacenamiento_id, $capacidad, $estado);

                    if ($resultado) {
                        $actualizaciones_exitosas++;
                        
                        // Si está activo, recalcular estimación
                        if ($estado === 'activo') {
                            $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorId($almacenamiento_id);
                            if ($almacenamiento) {
                                $this->Mcola_estimada->actualizarEstimacionAutomatica(
                                    $almacenamiento['sucursal_id'],
                                    $almacenamiento['combustible_id'],
                                    $capacidad
                                );
                            }
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
            if ($actualizaciones_exitosas > 0 && empty($errores)) {
                $_SESSION['success'] = "Almacenamientos actualizados correctamente ($actualizaciones_exitosas tanques)";
            } elseif ($actualizaciones_exitosas > 0 && !empty($errores)) {
                $_SESSION['warning'] = "Se actualizaron $actualizaciones_exitosas tanques, pero hubo errores: " . implode(', ', $errores);
            } else {
                $_SESSION['error'] = "Error al actualizar almacenamientos: " . implode(', ', $errores);
            }

            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en actualizar almacenamientos: " . $e->getMessage());
            $_SESSION['error'] = "Error al actualizar almacenamientos: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;
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
            
            // Obtener almacenamientos completos para análisis
            $almacenamientos_completos = $this->Malmacenamiento->obtenerAlmacenamientosCompletos();
            
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
                'almacenamientos_completos' => $almacenamientos_completos,
                'estadisticas_por_sucursal' => $estadisticas_por_sucursal
            ];

            require_once __DIR__ . '/../view/Valmacenamiento/reportes.php';

        } catch (Exception $e) {
            error_log("Error en reportes almacenamiento: " . $e->getMessage());
            $this->mostrarError("Error al cargar los reportes");
        }
    }

    // Buscar almacenamientos
    public function buscar() {
        try {
            $criterios = [];
            
            if (!empty($_GET['estado'])) {
                $criterios['estado'] = $_GET['estado'];
            }
            
            if (!empty($_GET['sucursal_id']) && is_numeric($_GET['sucursal_id'])) {
                $criterios['sucursal_id'] = intval($_GET['sucursal_id']);
            }
            
            if (!empty($_GET['combustible_id']) && is_numeric($_GET['combustible_id'])) {
                $criterios['combustible_id'] = intval($_GET['combustible_id']);
            }
            
            if (!empty($_GET['capacidad_minima']) && is_numeric($_GET['capacidad_minima'])) {
                $criterios['capacidad_minima'] = floatval($_GET['capacidad_minima']);
            }

            $almacenamientos = $this->Malmacenamiento->buscarAlmacenamientos($criterios);
            
            // Obtener datos para los filtros
            $sucursales_result = $this->Msucursal->obtenerSucursales();
            $combustibles_result = $this->Mcombustible->obtenerCombustible();

            $sucursales = [];
            $combustibles = [];
            if ($sucursales_result && is_object($sucursales_result)) {
                while ($row = $sucursales_result->fetch_assoc()) {
                    $sucursales[] = $row;
                }
            }

            if ($combustibles_result && is_object($combustibles_result)) {
                while ($row = $combustibles_result->fetch_assoc()) {
                    $combustibles[] = $row;
                }
            }

            $datos_vista = [
                'almacenamientos' => $almacenamientos,
                'sucursales' => $sucursales,
                'combustibles' => $combustibles,
                'criterios' => $criterios
            ];
            require_once __DIR__ . '/../view/Valmacenamiento/buscar.php';
        } catch (Exception $e) {
            error_log("Error en buscar almacenamientos: " . $e->getMessage());
            $this->mostrarError("Error al buscar almacenamientos: " . $e->getMessage());
        }
    }
    // Mostrar error genérico

    private function mostrarError($mensaje) {
        $_SESSION['error'] = $mensaje;
        header("Location: index.php?action=almacenamiento");
        exit;
    }
    // Método para manejar excepciones no capturadas
    public function manejarExcepcion($exception) {
        error_log("Excepción no capturada: " . $exception->getMessage());
        $this->mostrarError("Ocurrió un error inesperado. Por favor, inténtelo de nuevo más tarde.");
    }
}