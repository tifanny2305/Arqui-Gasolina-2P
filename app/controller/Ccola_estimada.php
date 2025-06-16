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

    // Listar sucursales para selección de estimación
    public function listarSucursales() {
        try {
            $sucursales = $this->Msucursal->obtenerSucursales();
            
            // Convertir resultado a array si es necesario
            if ($sucursales && is_object($sucursales)) {
                $sucursales_array = [];
                while ($row = $sucursales->fetch_assoc()) {
                    $sucursales_array[] = $row;
                }
                $sucursales = $sucursales_array;
            }

            require_once __DIR__ . '/../view/Vcola_estimada/index.php';
        } catch (Exception $e) {
            error_log("Error en listarSucursales: " . $e->getMessage());
            $this->mostrarError("Error al cargar las sucursales");
        }
    }

    // Mostrar estimación de una sucursal específica
    public function mostrarSucursal($sucursal_id) {
        try {
            // Validar entrada
            if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
                throw new Exception("ID de sucursal inválido");
            }
            
            // Verificar que la sucursal existe
            $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
            if (!$sucursal) {
                throw new Exception("Sucursal no encontrada");
            }
            
            // Obtener estimaciones
            $estimaciones = $this->Mcola_estimada->obtenerEstimacionesSucursal($sucursal_id);

            // Preparar datos para la vista
            $datos_vista = [
                'sucursal' => $sucursal,
                'estimaciones' => $estimaciones
            ];
                    
            require_once __DIR__ . '/../view/Vcola_estimada/estimacion.php';

        } catch (Exception $e) {
            error_log("Error en mostrarSucursal: " . $e->getMessage());
            $this->mostrarError($e->getMessage());
        }
    }

    // Actualizar estimaciones de una sucursal
    public function actualizarEstimacionesSucursal($sucursal_id) {
        try {
            // Validar entrada
            if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
                throw new Exception("ID de sucursal inválido");
            }

            // Verificar que la sucursal existe
            $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
            if (!$sucursal) {
                throw new Exception("Sucursal no encontrada");
            }

            // Actualizar estimaciones
            $resultado = $this->Mcola_estimada->actualizarEstimacionesSucursal($sucursal_id);
            
            if ($resultado) {
                // Redirigir con mensaje de éxito
                header("Location: index.php?action=mostrar_cola&id=$sucursal_id&success=1");
                exit;
            } else {
                throw new Exception("Error al actualizar las estimaciones");
            }

        } catch (Exception $e) {
            error_log("Error en actualizarEstimacionesSucursal: " . $e->getMessage());
            // Redirigir con mensaje de error
            header("Location: index.php?action=mostrar_cola&id=$sucursal_id&error=" . urlencode($e->getMessage()));
            exit;
        }
    }

    // Mostrar página de error
    private function mostrarError($mensaje) {
        $error = $mensaje;
        require_once __DIR__ . '/../view/error.php';
    }

    // Método para recalcular estimaciones cuando se actualiza un tanque
    public function recalcularEstimacionPorTanque($sucursal_id, $combustible_id, $nueva_capacidad) {
        try {
            // Validar entradas
            if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
                throw new Exception("ID de sucursal inválido");
            }
            
            if (!is_numeric($combustible_id) || $combustible_id <= 0) {
                throw new Exception("ID de combustible inválido");
            }
            
            if (!is_numeric($nueva_capacidad) || $nueva_capacidad < 0) {
                throw new Exception("Capacidad inválida");
            }

            // Actualizar estimación específica
            $resultado = $this->Mcola_estimada->actualizarEstimacionAutomatica(
                $sucursal_id,
                $combustible_id,
                $nueva_capacidad
            );

            return $resultado;

        } catch (Exception $e) {
            error_log("Error en recalcularEstimacionPorTanque: " . $e->getMessage());
            return false;
        }
    }
}