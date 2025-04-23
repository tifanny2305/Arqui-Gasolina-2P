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

        require_once __DIR__ . '/../view/Vcola_estimada/index.php';
    }

    public function mostrarSucursal($sucursal_id) {
        if (!is_numeric($sucursal_id) || $sucursal_id <= 0) {
            throw new Exception("ID de sucursal inválido");
        }
        
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        if (!$sucursal) {
            throw new Exception("Sucursal no encontrada");
        }
        
        //$tanques = $this->Msucursal->obtenerTanquesSucursal($sucursal_id);

        $estimaciones = $this->Mcola_estimada->obtenerEstimacionesSucursal($sucursal_id);

        $datos_vista = [
            'estimaciones' => $estimaciones
        ];
                
        require_once __DIR__ . '/../view/Vcola_estimada/estimacion.php';
    }

    /*public function actualizarEstimacionesSucursal($sucursal_id) {
        $combustibles = $this->Msucursal->obtenerTanquesSucursal($sucursal_id);
        foreach ($combustibles as $combustible) {
            if ($combustible['estado'] === 'activo') {
                $this->Mcola_estimada->actualizarEstimacionAutomatica(
                    $sucursal_id,
                    $combustible['id'],
                    $combustible['capacidad_actual']
                );
            }
        }
        
    }*/
    

    
}
?>