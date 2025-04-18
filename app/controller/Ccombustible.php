<?php
require_once __DIR__ . '/../model/Mcombustible.php';

class Ccombustible {
    private $Mcombustible;

    public function __construct() {
        $this->Mcombustible = new Mcombustible();
    }

    public function indexC() {
        $tipos = $this->Mcombustible->obtenerCombustible();
        require_once __DIR__ . '/../view/Vcombustible/index.php';
    }

    public function mostrar_crear_combustible()
    {
        require_once __DIR__ . '/../view/Vcombustible/create.php';
    }

    public function crear_combustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            $litros = $_POST['litros'];

            if ($this->Mcombustible->crearCombustible($tipo, $litros)) {
                header("Location: index.php?action=combustibles");
            } else {
                echo "Error al registrar el tipo de combustible.";
            }
        } else {
            require_once __DIR__ . '/../view/Vcombustible/create.php';
        }
    }

    public function editar_combustible() {
        if (isset($_GET['id'])) {
            $tipo = $this->Mcombustible->obtenerCombustiblePorId($_GET['id']);
            require_once __DIR__ . '/../view/Vcombustible/edit.php';
        }
    }

    public function actualizar_combustible() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $tipo = $_POST['tipo'];
            $litros = $_POST['litros'];
    
            if ($this->Mcombustible->actualizarCombustible($id, $tipo, $litros)) {
                header("Location: index.php?action=combustibles");
                exit;
            } else {
                echo "Error al actualizar el combustible";
            }
        }
    }

    public function eliminar_combustible() {
        if (isset($_GET['id'])) {
            $this->Mcombustible->eliminarCombustible($_GET['id']);
            header("Location: index.php?action=combustibles");
            exit;
        }
    }
}
