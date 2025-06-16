<?php
require_once __DIR__ . '/../model/Msucursal.php';
require_once __DIR__ . '/../model/Mcombustible.php';
require_once __DIR__ . '/../model/Msucursal_combustible.php';

class Csucursal
{
    private $Msucursal;
    private $Mcombustible;
    private $Msucursal_combustible;

    public function __construct()
    {
        $this->Msucursal = new Msucursal();
        $this->Mcombustible = new Mcombustible();
        $this->Msucursal_combustible = new Msucursal_combustible();
    }

    public function mostrar_crear_sucursal()
    {
        try {
            $combustibles = $this->Mcombustible->obtenerCombustible();
            require_once __DIR__ . '/../view/Vsucursal/create.php';
        } catch (Exception $e) {
            error_log("Error en mostrar_crear_sucursal: " . $e->getMessage());
            $_SESSION['error'] = "Error al cargar el formulario de creación";
            header("Location: index.php?action=sucursales");
            exit;
        }
    }

    public function indexS()
    {
        try {
            $sucursales_result = $this->Msucursal->obtenerSucursales();
            
            // Convertir resultado a array
            $sucursales = [];
            if ($sucursales_result && is_object($sucursales_result)) {
                while ($row = $sucursales_result->fetch_assoc()) {
                    $sucursales[] = $row;
                }
            }
            
            require_once __DIR__ . '/../view/Vsucursal/index.php';
        } catch (Exception $e) {
            error_log("Error en indexS: " . $e->getMessage());
            $error = "Error al cargar las sucursales";
            require_once __DIR__ . '/../view/error.php';
        }
    }

    public function crear_sucursal()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header("Location: index.php?action=crear_sucursal");
            exit;
        }

        try {
            // Validar datos básicos
            $nombre = trim($_POST['nombre'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $bombas = intval($_POST['bombas'] ?? 0);
            $combustibles_seleccionados = $_POST['combustibles'] ?? [];

            if (empty($nombre) || empty($ubicacion) || $bombas <= 0) {
                throw new Exception("Por favor, complete todos los campos obligatorios");
            }

            // Crear sucursal
            $sucursal_id = $this->Msucursal->crearSucursal($nombre, $ubicacion, $bombas);
            
            if (!$sucursal_id) {
                throw new Exception("Error al crear la sucursal");
            }

            // Asignar combustibles seleccionados
            $combustibles_asignados = 0;
            if (!empty($combustibles_seleccionados)) {
                foreach ($combustibles_seleccionados as $combustible_id) {
                    if ($this->Msucursal_combustible->asignarCombustible($sucursal_id, $combustible_id)) {
                        $combustibles_asignados++;
                    }
                }
            }

            $_SESSION['success'] = "Sucursal creada exitosamente. Se asignaron $combustibles_asignados tipos de combustible.";
            header("Location: index.php?action=sucursales");
            exit;

        } catch (Exception $e) {
            error_log("Error en crear_sucursal: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?action=crear_sucursal");
            exit;
        }
    }

    public function editar_sucursal()
    {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id || !is_numeric($id)) {
                throw new Exception("ID de sucursal inválido");
            }

            $sucursal = $this->Msucursal->obtenerSucursalPorId($id);
            if (!$sucursal) {
                throw new Exception("Sucursal no encontrada");
            }

            $combustibles_result = $this->Mcombustible->obtenerCombustible();
            $combustibles = [];
            if ($combustibles_result && is_object($combustibles_result)) {
                while ($row = $combustibles_result->fetch_assoc()) {
                    $combustibles[] = $row;
                }
            }

            $combustibles_seleccionados_result = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($id);
            $combustibles_seleccionados = [];
            foreach ($combustibles_seleccionados_result as $combustible) {
                $combustibles_seleccionados[] = $combustible['id'];
            }

            require_once __DIR__ . '/../view/Vsucursal/edit.php';

        } catch (Exception $e) {
            error_log("Error en editar_sucursal: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?action=sucursales");
            exit;
        }
    }

    public function actualizar_sucursal()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            header("Location: index.php?action=sucursales");
            exit;
        }

        try {
            $id = $_POST['id'] ?? null;
            $nombre = trim($_POST['nombre'] ?? '');
            $ubicacion = trim($_POST['ubicacion'] ?? '');
            $bombas = intval($_POST['bombas'] ?? 0);
            $combustibles_seleccionados = $_POST['combustibles'] ?? [];

            if (!$id || !is_numeric($id)) {
                throw new Exception("ID de sucursal inválido");
            }

            if (empty($nombre) || empty($ubicacion) || $bombas <= 0) {
                throw new Exception("Por favor, complete todos los campos obligatorios");
            }

            // Actualizar datos básicos de la sucursal
            $resultado = $this->Msucursal->actualizarSucursal($id, $nombre, $ubicacion, $bombas);
            
            if (!$resultado) {
                throw new Exception("Error al actualizar la sucursal");
            }

            // Gestionar combustibles
            $combustibles_actuales = $this->Msucursal_combustible->obtenerCombustiblesPorSucursal($id);
            $combustibles_actuales_ids = array_column($combustibles_actuales, 'id');
            
            // Identificar cambios
            $para_eliminar = array_diff($combustibles_actuales_ids, $combustibles_seleccionados);
            $para_agregar = array_diff($combustibles_seleccionados, $combustibles_actuales_ids);
            
            // Eliminar combustibles no seleccionados
            foreach ($para_eliminar as $combustible_id) {
                $this->Msucursal_combustible->eliminarCombustible($id, $combustible_id);
            }
            
            // Agregar nuevos combustibles
            foreach ($para_agregar as $combustible_id) {
                $this->Msucursal_combustible->asignarCombustible($id, $combustible_id);
            }

            $_SESSION['success'] = "Sucursal actualizada exitosamente";
            header("Location: index.php?action=sucursales");
            exit;

        } catch (Exception $e) {
            error_log("Error en actualizar_sucursal: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $id = $_POST['id'] ?? '';
            header("Location: index.php?action=editar_sucursal&id=$id");
            exit;
        }
    }

    public function eliminar_sucursal()
    {
        try {
            $id = $_GET['id'] ?? null;
            
            if (!$id || !is_numeric($id)) {
                throw new Exception("ID de sucursal inválido");
            }

            // Verificar que la sucursal existe
            $sucursal = $this->Msucursal->obtenerSucursalPorId($id);
            if (!$sucursal) {
                throw new Exception("Sucursal no encontrada");
            }

            // Eliminar sucursal (CASCADE eliminará automáticamente las relaciones)
            $resultado = $this->Msucursal->eliminarSucursal($id);
            
            if ($resultado) {
                $_SESSION['success'] = "Sucursal '{$sucursal['nombre']}' eliminada exitosamente";
            } else {
                throw new Exception("Error al eliminar la sucursal");
            }

            header("Location: index.php?action=sucursales");
            exit;

        } catch (Exception $e) {
            error_log("Error en eliminar_sucursal: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?action=sucursales");
            exit;
        }
    }
}
?>