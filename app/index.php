<?php
require_once __DIR__ . '/controller/Csucursal.php';
require_once __DIR__ . '/controller/Ccombustible.php';
require_once __DIR__ . '/controller/Csucursal_combustible.php';
require_once __DIR__ . '/controller/Ccola_estimada.php';
require_once __DIR__ . '/controller/Cparametros_combustible.php';
require_once __DIR__ . '/controller/Calmacenamiento.php';

$Csucursal = new Csucursal();
$Ccombustible = new Ccombustible();
$Ccola_estimada = new Ccola_estimada();
$Cparametros_combustible = new Cparametros_combustible();
$Calmacenamiento = new Calmacenamiento();

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
    
    // Acciones para estimar colas
    case 'estimacion_cola':
        $Ccola_estimada->listarSucursales();
        break;
    case 'mostrar_cola':
        $Ccola_estimada->mostrarSucursal($_GET['id']);
        break;
    
    case 'actualizar_estimaciones':
        $Ccola_estimada = new Ccola_estimada();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $Ccola_estimada->actualizarEstimacionesSucursal($id);
        } else {
            header('Location: index.php?action=estimacion_cola');
            exit;
        }
        break;
    

    // Acciones para parametro combustible
    case 'parametros_combustible':
        $Cparametros_combustible->listarCombustibles();
        break;

    case 'editar_parametros':
        $Cparametros_combustible->editarParametros();
        break;
    
    case 'guardar_parametros':
        $Cparametros_combustible->guardar();
        break;
    
    default:
        echo "Acción no válida.";
        break;

    // Acciones para almacenamiento
    case 'almacenamiento':
        $Calmacenamiento->index();
        break;

    case 'gestionar_almacenamiento':
        $Calmacenamiento->gestionar();
        break;

    case 'actualizar_almacenamiento':
        $Calmacenamiento->actualizar();
        break;

    case 'detalle_almacenamiento':
        $Calmacenamiento->detalle();
        break;
}
?>
