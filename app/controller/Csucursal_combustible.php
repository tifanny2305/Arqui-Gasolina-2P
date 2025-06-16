<?php
require_once __DIR__ . '/../model/Msucursal_combustible.php';
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';
require_once __DIR__ . '/../model/Mcola_estimada.php';

class Csucursal_combustible {
    private $Msucursal_combustible;
    private $Msucursal;
    private $Mcola_estimada;

    public function __construct() {
        $this->Msucursal_combustible = new Msucursal_combustible();
        $this->Msucursal = new Msucursal();
        $this->Mcola_estimada = new Mcola_estimada();
    }

    // Listar sucursales para gestión de tanques
    public function listarSucursales() {
        try {
            $sucursales_result = $this->Msucursal->obtenerSucursales();
            
            // Convertir resultado a array
            $sucursales = [];
            if ($sucursales_result && is_object($sucursales_result)) {
                while ($row = $sucursales_result->fetch_assoc()) {
                    $sucursales[] = $row;
                }
            }

            require_once __DIR__ . '/../view/Vsucursal_combustible/index.php';

        } catch (Exception $e) {
            error_log("Error en listarSucursales: " . $e->getMessage());
            $this->mostrarError("Error al cargar las sucursales");
        }
    }

    // Obtener datos de sucursal y combustibles para gestión de tanques
    public function tanques() {
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

            // Obtener combustibles de la sucursal
            $combustibles = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($sucursal_id);
            
            require_once __DIR__ . '/../view/Vsucursal_combustible/gestionar.php';

        } catch (Exception $e) {
            error_log("Error en tanques: " . $e->getMessage());
            $this->mostrarError($e->getMessage());
        }
    }

    // Actualizar tanques de combustible
    public function actualizarTanques() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=tanques");
            exit;
        }

        try {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $combustibles_data = $_POST['combustibles'] ?? [];

            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (empty($combustibles_data)) {
                throw new Exception("No se recibieron datos de combustibles");
            }

            $errores = [];
            $actualizaciones_exitosas = 0;

            foreach ($combustibles_data as $combustible_id => $datos) {
                try {
                    // Validar datos
                    $capacidad_actual = floatval($datos['capacidad_actual'] ?? 0);
                    $estado = $datos['estado'] ?? 'inactivo';

                    if ($capacidad_actual < 0) {
                        throw new Exception("Capacidad inválida para combustible ID: $combustible_id");
                    }

                    // Actualizar tanque en la base de datos
                    $resultado = $this->Msucursal_combustible->actualizarTanque(
                        $sucursal_id,
                        $combustible_id,
                        $capacidad_actual,
                        $estado
                    );

                    if ($resultado) {
                        $actualizaciones_exitosas++;
                        
                        // Si el combustible está activo, actualizar estimación
                        if ($estado === 'activo') {
                            $this->Mcola_estimada->actualizarEstimacionAutomatica(
                                $sucursal_id,
                                $combustible_id,
                                $capacidad_actual
                            );
                        }
                    } else {
                        $errores[] = "Error al actualizar combustible ID: $combustible_id";
                    }

                } catch (Exception $e) {
                    $errores[] = $e->getMessage();
                    error_log("Error actualizando combustible $combustible_id: " . $e->getMessage());
                }
            }

            // Preparar mensaje de respuesta
            session_start();
            if ($actualizaciones_exitosas > 0 && empty($errores)) {
                $_SESSION['success'] = "Configuración de tanques actualizada correctamente";
            } elseif ($actualizaciones_exitosas > 0 && !empty($errores)) {
                $_SESSION['warning'] = "Algunos tanques se actualizaron correctamente, pero hubo errores: " . implode(', ', $errores);
            } else {
                $_SESSION['error'] = "Error al actualizar tanques: " . implode(', ', $errores);
            }

            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en actualizarTanques: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al actualizar tanques: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;
        }
    }
     
    // Asignar combustible a sucursal
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=tanques");
            exit;
        }

        try {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $combustible_id = $_POST['combustible_id'] ?? null;

            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (!$combustible_id || !is_numeric($combustible_id)) {
                throw new Exception("ID de combustible inválido");
            }

            $resultado = $this->Msucursal_combustible->asignarCombustible($sucursal_id, $combustible_id);

            session_start();
            if ($resultado) {
                $_SESSION['success'] = "Combustible asignado correctamente";
            } else {
                $_SESSION['error'] = "Error al asignar el combustible";
            }

            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en asignarCombustible: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al asignar combustible: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;
        }
    }

    // Eliminar combustible de sucursal
    public function eliminarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=tanques");
            exit;
        }

        try {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $combustible_id = $_POST['combustible_id'] ?? null;

            if (!$sucursal_id || !is_numeric($sucursal_id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (!$combustible_id || !is_numeric($combustible_id)) {
                throw new Exception("ID de combustible inválido");
            }

            $resultado = $this->Msucursal_combustible->eliminarCombustible($sucursal_id, $combustible_id);

            session_start();
            if ($resultado) {
                $_SESSION['success'] = "Combustible eliminado correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar el combustible";
            }

            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;

        } catch (Exception $e) {
            error_log("Error en eliminarCombustible: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al eliminar combustible: " . $e->getMessage();
            
            $sucursal_id = $_POST['sucursal_id'] ?? '';
            header("Location: index.php?action=gestionar_tanques&id=$sucursal_id");
            exit;
        }
    }

    // Mostrar página de error
    private function mostrarError($mensaje) {
        $error = $mensaje;
        require_once __DIR__ . '/../view/error.php';
    }

    // Obtener tanques de una sucursal (método auxiliar)
    public function obtenerTanquesSucursal($sucursal_id) {
        try {
            if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
                throw new Exception("ID de sucursal inválido");
            }

            return $this->Msucursal_combustible->obtenerTanquesSucursal($sucursal_id);

        } catch (Exception $e) {
            error_log("Error en obtenerTanquesSucursal: " . $e->getMessage());
            return [];
        }
    }
}