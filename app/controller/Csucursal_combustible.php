<?php
require_once __DIR__ . '/../model/Msucursal_combustible.php';
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';

class Csucursal_combustible {
    private $model;
    private $modelSucursal;
    private $modelCombustible;

    public function __construct() {
        $this->model = new Msucursal_combustible();
        $this->modelSucursal = new Msucursal();
        $this->modelCombustible = new Mcombustible();
    }

    public function listarSucursales() {
        $sucursales = $this->modelSucursal->obtenerSucursales();
        require_once __DIR__ . '/../view/Vsucursal_combustible/index.php';
    }

    public function tanques() {
        $sucursal_id = $_GET['id'];
        $sucursal = $this->modelSucursal->obtenerSucursalPorId($sucursal_id);
        $combustibles = $this->model->obtenerCombustiblesPorSucursal($sucursal_id);
        
        require_once __DIR__ . '/../view/Vsucursal_combustible/gestionar.php';
    }

    public function actualizarTanques() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'];
            
            foreach ($_POST['combustibles'] as $combustible_id => $datos) {
                $this->model->actualizarTanque(
                    $sucursal_id,
                    $combustible_id,
                    $datos['capacidad_max'],
                    $datos['capacidad_min'],
                    $datos['estado']
                );
            }
            
            $_SESSION['success'] = "Configuración de tanques actualizada correctamente";
            header("Location: index.php?action=tanques&id=$sucursal_id");
            exit;
        }
    }
    
    //Procesa la asignación de combustibles
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'];
            $combustible_id = $_POST['combustible_id'];
            $duracion = $_POST['duracion_tanque'];

            if ($this->model->crearRelacion($sucursal_id, $combustible_id, $duracion)) {
                header("Location: index.php?action=ver_sucursal&id=$sucursal_id");
                exit;
            } else {
                echo "Error al asignar el combustible";
            }
        }
    }

    public function eliminarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'] ?? null;
            $combustible_id = $_POST['combustible_id'] ?? null;

            // Depuración - verifica los valores recibidos
            error_log("Intentando eliminar combustible: sucursal_id=$sucursal_id, combustible_id=$combustible_id");
    
            if (!$sucursal_id || !$combustible_id) {
                $_SESSION['error'] = "Datos incompletos para la eliminación";
                header("Location: index.php?action=tanques");
                exit;
            }
    
            if ($this->model->eliminarCombustible($sucursal_id, $combustible_id)) {
                $_SESSION['success'] = "Combustible eliminado correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar el combustible";
            }
    
            header("Location: index.php?action=tanques&id=$sucursal_id");
            exit;
        }
    }

    
}
?>