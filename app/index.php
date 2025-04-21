<?php
require_once __DIR__ . '/controller/Csucursal.php';
require_once __DIR__ . '/controller/Ccombustible.php';
require_once __DIR__ . '/controller/Csucursal_combustible.php';
require_once __DIR__ . '/controller/Ccola_estimada.php';

$Csucursal = new Csucursal();
$Ccombustible = new Ccombustible();
$Csucursal_combustible = new Csucursal_combustible();
$Ccola_estimada = new Ccola_estimada();

$action = $_GET['action'] ?? 'menu';

switch ($action) {
    // Menú principal
    case 'menu':
        require_once __DIR__ . '/view/menu.php';
        break;
        
    // Acciones para sucursales
    case 'sucursales':
        $Csucursal->indexS();
        break;
    case 'crear_sucursal':
        $Csucursal->mostrar_crear_sucursal();
        break;  
    case 'registrar_sucursal':
        $Csucursal->crear_sucursal();
        break;
    case 'editar_sucursal':
        $Csucursal->editar_sucursal();
        break;
    case 'actualizar_sucursal':
        $Csucursal->actualizar_sucursal();
        break;
    case 'eliminar_sucursal':
        $Csucursal->eliminar_sucursal();
        break;
        
    // Acciones para combustibles
    case 'combustibles':
        $Ccombustible->indexC();
        break;
    case 'crear_combustible':
        $Ccombustible->mostrar_crear_combustible();
        break;
    case 'registrar_combustible':
        $Ccombustible->crear_combustible();
        break;
    case 'editar_combustible':
        $Ccombustible->editar_combustible();
        break;
    case 'actualizar_combustible':
        $Ccombustible->actualizar_combustible();
        break;
    case 'eliminar_combustible':
        $Ccombustible->eliminar_combustible();
        break;
    
    // Acciones para asignar combustibles a sucursales
    case 'tanques':
        $Csucursal_combustible->listarSucursales();
        break;
    case 'gestionar_tanques':
        $Csucursal_combustible->tanques();
        break;
    case 'actualizar_tanques':
        $Csucursal_combustible->actualizarTanques();
        break;
    case 'eliminar_combustible_sucursal':
        $Csucursal_combustible->eliminarCombustible();
        break;

    // Acciones para estimar colas
    case 'estimacion_cola':
        $Ccola_estimada->listarSucursales();
        break;
    case 'mostrar_cola':
        if (isset($_GET['id'])) {
            $Ccola_estimada->mostrarSucursal($_GET['id']);
        }
        break;
    default:
        echo "Acción no válida.";
        break;
}

?>
