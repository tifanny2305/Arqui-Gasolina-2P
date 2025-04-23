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

    public function listarSucursales() {
        $sucursales = $this->Msucursal->obtenerSucursales();
        require_once __DIR__ . '/../view/Vsucursal_combustible/index.php';
    }

    //Obtiene los datos de sucursal y el combustible
    public function tanques() {
        $sucursal_id = $_GET['id'];
        $sucursal = $this->Msucursal->obtenerSucursalPorId($sucursal_id);
        $combustibles = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($sucursal_id);
        
        require_once __DIR__ . '/../view/Vsucursal_combustible/gestionar.php';
    }

    //Actualizamos los campos
    public function actualizarTanques() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'];
            
            foreach ($_POST['combustibles'] as $combustible_id => $datos) {
                //Actualizar el tanque en la base de datos
                $this->Msucursal_combustible->actualizarTanque(
                    $sucursal_id,
                    $combustible_id,
                    $datos['capacidad_actual'],
                    $datos['estado']
                );
                
                //Si el combustible está activo, actualizar estimación
                if ($datos['estado'] === 'activo') {
                    $this->Mcola_estimada->actualizarEstimacionAutomatica(
                        $sucursal_id,
                        $combustible_id,
                        $datos['capacidad_actual']
                    );
                }
            }
            
            $_SESSION['success'] = "Configuración de tanques actualizada correctamente";
            header("Location: index.php?action=tanques&id=$sucursal_id");
            exit;
        }
    }
     
    //Asignamos el combustible a la sucursal
    public function asignarCombustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sucursal_id = $_POST['sucursal_id'];
            $combustible_id = $_POST['combustible_id'];
            $duracion = $_POST['duracion_tanque'];

            if ($this->Msucursal_combustible->crearRelacion($sucursal_id, $combustible_id, $duracion)) {
                header("Location: index.php?action=ver_sucursal&id=$sucursal_id");
                exit;
            } else {
                echo "Error al asignar el combustible";
            }
        }
    }

    //Eliminamos el combustible de la sucursal
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
    
            if ($this->Msucursal_combustible->eliminarCombustible($sucursal_id, $combustible_id)) {
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