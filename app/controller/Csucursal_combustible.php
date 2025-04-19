<?php
require_once __DIR__ . '/../model/MsucursalCombustible.php';
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';

class CsucursalCombustible {
    private $model;
    private $modelSucursal;
    private $modelCombustible;

    public function __construct() {
        $this->model = new Msucursal_combustible();
        $this->modelSucursal = new Msucursal();
        $this->modelCombustible = new Mcombustible();
    }

    /**
     * Muestra el formulario para asignar combustibles
     */
    public function mostrarAsignacion() {
        $sucursales = $this->modelSucursal->obtenerSucursales();
        $combustibles = $this->modelCombustible->obtenerCombustible();
        require_once __DIR__ . '/../view/Vsucursal_combustible/asignar.php';
    }

    /**
     * Procesa la asignación de combustibles
     */
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_sucursal = $_POST['id_sucursal'];
            $id_combustible = $_POST['id_combustible'];
            $duracion = $_POST['duracion_tanque'];

            if ($this->model->crearRelacion($id_sucursal, $id_combustible, $duracion)) {
                header("Location: index.php?action=ver_sucursal&id=$id_sucursal");
                exit;
            } else {
                echo "Error al asignar el combustible";
            }
        }
    }

    /**
     * Muestra los combustibles de una sucursal
     */
    public function verCombustiblesSucursal() {
        if (isset($_GET['id_sucursal'])) {
            $id_sucursal = $_GET['id_sucursal'];
            $sucursal = $this->modelSucursal->obtenerSucursalPorId($id_sucursal);
            $combustibles = $this->model->obtenerPorSucursal($id_sucursal);
            
            require_once __DIR__ . '/../view/Vsucursal_combustible/listar.php';
        }
    }

    /**
     * Cambia el estado de un combustible
     */
    public function cambiarEstado() {
        if (isset($_POST['id_sucursal']) && isset($_POST['id_combustible'])) {
            $id_sucursal = $_POST['id_sucursal'];
            $id_combustible = $_POST['id_combustible'];
            $estado = $_POST['estado'];

            if ($this->model->actualizarEstado($id_sucursal, $id_combustible, $estado)) {
                header("Location: index.php?action=ver_combustibles_sucursal&id_sucursal=$id_sucursal");
                exit;
            }
        }
        echo "Error al cambiar el estado";
    }
}
?>