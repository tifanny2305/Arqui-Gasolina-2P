<?php
require_once __DIR__ . '/../model/Malmacenamiento.php';
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcola_estimada.php';

class Calmacenamiento {
    private $Malmacenamiento;
    private $Msucursal;
    private $Mcola_estimada;

    public function __construct() {
        $this->Malmacenamiento = new Malmacenamiento();
        $this->Msucursal = new Msucursal();
        $this->Mcola_estimada = new Mcola_estimada();
    }

    public function index() {
        $estadisticas = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento();
        
        $sucursales_result = $this->Msucursal->obtenerSucursales();
        $sucursales = [];
        if ($sucursales_result) {
            while ($row = $sucursales_result->fetch_assoc()) {
                $row['almacenamientos'] = $this->Malmacenamiento->obtenerAlmacenamientosPorSucursal($row['id']);
                $row['estadisticas'] = $this->Malmacenamiento->obtenerEstadisticasAlmacenamiento($row['id']);
                $sucursales[] = $row;
            }
        }

        require_once __DIR__ . '/../view/Valmacenamiento/index.php';
    }

    public function gestionar() {
        $sucursal_id = $_GET['id'];
        
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        $almacenamientos = $this->Malmacenamiento->obtenerAlmacenamientosPorSucursal($sucursal_id);
        
        require_once __DIR__ . '/../view/Valmacenamiento/gestionar.php';
    }

    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'];
            $almacenamientos_data = $_POST['almacenamientos'];

            foreach ($almacenamientos_data as $almacenamiento_id => $datos) {
                $capacidad = floatval($datos['cap_actual']);
                $estado = $datos['estado'];

                $this->Malmacenamiento->actualizarAlmacenamiento($almacenamiento_id, $capacidad, $estado);
                
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
            }
            
            header("Location: index.php?action=gestionar_almacenamiento&id=$sucursal_id");
            exit;
        }
    }

    public function detalle() {
        $almacenamiento_id = $_GET['id'];
        
        $almacenamiento = $this->Malmacenamiento->obtenerAlmacenamientoPorId($almacenamiento_id);
        
        $estimacion = [];
        if ($almacenamiento && $almacenamiento['estado'] === 'activo') {
            $estimaciones = $this->Mcola_estimada->obtenerEstimacionesSucursal($almacenamiento['sucursal_id']);
            foreach ($estimaciones as $est) {
                if ($est['tipo_combustible'] === $almacenamiento['combustible_tipo']) {
                    $estimacion = $est;
                    break;
                }
            }
        }

        require_once __DIR__ . '/../view/Valmacenamiento/detalle.php';
    }
}
?>