<?php
// Cargar el controlador según la acción
require_once __DIR__ . '/controller/Csucursal.php';

$controller = new Csucursal();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index(); // Listar sucursales
        break;
    case 'crear_sucursal':
        require_once __DIR__ . '/view/Vsucursal/create.php'; // Mostrar formulario
        break;
    case 'registrar_sucursal':
        $controller->crear_sucursal(); // Guardar nueva sucursal
        break;
    case 'editar_sucursal':
        $controller->editar_sucursal(); // Mostrar formulario de edición
        break;
    case 'actualizar_sucursal':
        $controller->actualizar_sucursal(); // Guardar cambios
        break;
    case 'eliminar_sucursal':
        $controller->eliminar_sucursal(); // Eliminar sucursal
        break;
    default:
        echo "Acción no válida.";
        break;
}

?>
