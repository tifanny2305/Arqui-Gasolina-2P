<?php
require_once __DIR__ . '/../model/Msucursal_combustible.php';
require_once __DIR__ . '/../model/Msucursal.php';

class Csucursal_combustible {
    private $Msucursal_combustible;
    private $Msucursal;

    public function __construct() {
        $this->Msucursal_combustible = new Msucursal_combustible();
        $this->Msucursal = new Msucursal();
    }

    // Vista para asignar combustibles a sucursal
    public function vistaAsignar() {
        $sucursales_result = $this->Msucursal->obtenerSucursales();
        require_once __DIR__ . '/../view/Vsucursal_combustible/asignar.php';
    }

    // Asignar combustible a sucursal
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=asignar_combustible");
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

            $resultado = $this->Msucursal_combustible->asignarRelacion($sucursal_id, $combustible_id);

            session_start();
            if ($resultado) {
                $_SESSION['success'] = "Relación sucursal-combustible registrada correctamente.";
            } else {
                $_SESSION['error'] = "No se pudo registrar la relación (puede que ya exista).";
            }

            header("Location: index.php?action=asignar_combustible");
            exit;

        } catch (Exception $e) {
            error_log("Error en asignarCombustible: " . $e->getMessage());
            session_start();
            $_SESSION['error'] = "Error al asignar combustible: " . $e->getMessage();
            header("Location: index.php?action=asignar_combustible");
            exit;
        }
    }

    // Mostrar página de error
    private function mostrarError($mensaje) {
        $error = $mensaje;
        require_once __DIR__ . '/../view/error.php';
    }
}