<?php
require_once __DIR__ . '/../model/Mparametros_combustible.php';
require_once __DIR__ . '/../model/Mcombustible.php';

class Cparametros_combustible
{
    private $Mparametros_combustible;
    private $Mcombustible;

    public function __construct()
    {
        // Pasa la conexión al modelo
        $this->Mparametros_combustible = new Mparametros_combustible();
        $this->Mcombustible = new Mcombustible();
    }

    public function listarCombustibles()
    {
        $combustibles = $this->Mcombustible->obtenerCombustible();
        require_once __DIR__ . '/../view/Vparametros_combustible/index.php';
    }

    public function editarParametros()
    {
        $combustible_id = $_GET['id'] ?? null;
        
        if (!$combustible_id) {
            header('Location: index.php?action=parametros_combustible');
            exit();
        }

        // Obtener datos del combustible
        $combustible = $this->Mcombustible->obtenerCombustiblePorId($combustible_id);
        
        if (!$combustible) {
            // Manejar error si el combustible no existe
            header('Location: index.php?action=parametros_combustible');
            exit();
        }

        // Obtener parámetros existentes (si existen)
        $parametros = $this->Mparametros_combustible->obtenerParametroPorId($combustible_id);

        require_once __DIR__ . '/../view/Vparametros_combustible/edit.php';
    }


    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $combustible_id = $_POST['combustible_id'];
            $consumo_por_auto = floatval($_POST['consumo_por_auto']);
            $tiempo_por_auto = $_POST['tiempo_por_auto'];
            $largo_vehiculo = floatval($_POST['largo_vehiculo']);
            
            // Verificar si ya existen parámetros para este combustible
            list($horas, $minutos) = explode(':', $tiempo_por_auto);
            $tiempo_por_auto = sprintf('%02d:%02d:00', intval($horas), intval($minutos));
            $parametros_existentes = $this->Mparametros_combustible->obtenerParametroPorId($combustible_id);
            
            if ($parametros_existentes) {
                // Actualizar registro existente
                $resultado = $this->Mparametros_combustible->actualizarParametro(
                    $combustible_id, 
                    $consumo_por_auto, 
                    $tiempo_por_auto, 
                    $largo_vehiculo
                );
            } else {
                // Crear nuevo registro
                $resultado = $this->Mparametros_combustible->crearParametroCombustible(
                    $combustible_id, 
                    $consumo_por_auto, 
                    $tiempo_por_auto, 
                    $largo_vehiculo
                );
            }
            
            if ($resultado === false) {
                // Manejar el error - podrías guardar un mensaje en sesión
                session_start();
                $_SESSION['error'] = "No se pudo guardar los parámetros";
            }
            
            // Redirigir a la vista de parámetros de combustible
            header('Location: index.php?action=parametros_combustible');
            exit();
        }
    }

}

