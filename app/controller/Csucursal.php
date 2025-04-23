<?php
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';
require_once __DIR__ . '/../model/Msucursal_combustible.php';
require_once __DIR__ . '/../model/Mcola_estimada.php';

class Csucursal
{
    private $Msucursal;
    private $Mcombustible;
    private $Msucursal_combustible;
    private $Ccola_estimada;

    public function __construct()
    {
        $this->Msucursal = new Msucursal();
        $this->Mcombustible = new Mcombustible();
        $this->Msucursal_combustible = new Msucursal_combustible();
        $this->Ccola_estimada = new Ccola_estimada();
    }

    public function mostrar_crear_sucursal()
    {
        $combustibles = $this->Mcombustible->obtenerCombustible();
        require_once __DIR__ . '/../view/Vsucursal/create.php';
    }

    public function indexS()
    {
        $sucursales = $this->Msucursal->obtenerSucursales(); 
        require_once __DIR__ . '/../view/Vsucursal/index.php';
    }

    public function crear_sucursal()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Datos básicos de la sucursal
            $nombre = $_POST['nombre'];
            $ubicacion = $_POST['ubicacion'];
            $bombas = $_POST['bombas'];

            if (empty($nombre) || empty($ubicacion) || empty($bombas)) {
                echo "Por favor, complete todos los campos.";
                return;
            }

            // Crear sucursal
            $sucursal_id = $this->Msucursal->crearSucursal($nombre, $ubicacion, $bombas);
            
            if (!$sucursal_id) {
                echo "Error al crear sucursal";
                return;
            }

            // Asignar combustibles seleccionados
            if (!empty($_POST['combustibles'])) {
                foreach ($_POST['combustibles'] as $combustible_id) {
                    $this->Msucursal_combustible->asignarCombustible($sucursal_id, $combustible_id);
                }
            }

            $this->Ccola_estimada->actualizarEstimacionesSucursal($sucursal_id);


            header("Location: index.php?action=sucursales");
            exit();
        }
    }

    public function editar_sucursal()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sucursal = $this->Msucursal->obtenerSucursalPorId($id);
            $combustibles = $this->Mcombustible->obtenerCombustible();
            $combustibles_seleccionados = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($id);

            if ($sucursal && $combustibles) {
                require_once __DIR__ . '/../view/Vsucursal/edit.php';
            } else {
                header("Location: index.php?action=sucursales&error=datos_no_encontrados");
                exit;
            }
        } else {
            header("Location: index.php?action=sucursales&error=id_no_proporcionado");
            exit;
        }
    }

    public function actualizar_sucursal()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $ubicacion = $_POST['ubicacion'];
            $bombas = $_POST['bombas'];
            $combustibles_seleccionados = $_POST['combustibles'] ?? [];

            $result = $this->Msucursal->actualizarSucursal($id, $nombre, $ubicacion, $bombas);
        
            if ($result) {
                //combustibles actualmente asignados
                $combustibles_actuales = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($id);
                $combustibles_actuales_ids = array_column($combustibles_actuales, 'combustible_id');
                
                //Identificar cambios
                $para_eliminar = array_diff($combustibles_actuales_ids, $combustibles_seleccionados);
                $para_agregar = array_diff($combustibles_seleccionados, $combustibles_actuales_ids);
                
                //Procesar eliminaciones
                foreach ($para_eliminar as $combustible_id) {
                    $this->Msucursal_combustible->eliminarCombustiblesDeSucursal($id);
                }
                
                //Procesar nuevas asignaciones
                foreach ($para_agregar as $combustible_id) {
                    $this->Msucursal_combustible->asignarCombustible($id, $combustible_id);
                }
                
                //Actualizar estados de los que se mantienen
                $para_actualizar = array_intersect($combustibles_actuales_ids, $combustibles_seleccionados);
                foreach ($para_actualizar as $combustible_id) {
                    $this->Msucursal_combustible->actualizarEstadoCombustible(
                        $id, 
                        $combustible_id, 
                        'disponible'
                    );
                }
                
                header("Location: index.php?action=sucursales&success=actualizado");
                exit;
            } else {
                header("Location: index.php?action=editar_sucursal&id=$id&error=actualizacion_fallida");
                exit;
            }
        }
    }

    public function eliminar_sucursal()
    {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Primero eliminamos las relaciones en sucursal_combustible
            $this->Msucursal_combustible->eliminarCombustiblesDeSucursal($id);

            if ($this->Msucursal->eliminarSucursal($id)) {
                header("Location: index.php?action=sucursales");
                exit;
            } else {
                echo "Error al eliminar la sucursal.";
            }
        } else {
            echo "ID no proporcionado.";
        }
    }

}
?>
