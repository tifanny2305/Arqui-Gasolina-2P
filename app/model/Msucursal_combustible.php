<?php
require_once(__DIR__ . '/../config/database.php');

class Msucursal_combustible
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Crear la relación N:M si no existe aún
    public function asignarRelacion($sucursal_id, $combustible_id)
    {
        $query = "SELECT id FROM sucursal_combustible WHERE sucursal_id = ? AND combustible_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();
        $existe = $stmt->get_result()->fetch_assoc();

        if ($existe) {
            return $existe['id'];
        }

        $insert = "INSERT INTO sucursal_combustible (sucursal_id, combustible_id) VALUES (?, ?)";
        $stmt = $this->db->prepare($insert);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);

        if ($stmt->execute()) {
            $sucursal_combustible_id = $this->db->insert_id;

            // Crear almacenamiento automático con capacidad 0 y estado activo
            require_once(__DIR__ . '/Malmacenamiento.php');
            $alm = new Malmacenamiento();
            $alm->crearAlmacenamiento($sucursal_combustible_id, 0, 'activo');

            return $sucursal_combustible_id;
        }

        return false;
    }

    public function existeRelacion($sucursal_id, $combustible_id)
    {
        $query = "SELECT id FROM sucursal_combustible WHERE sucursal_id = ? AND combustible_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $sucursal_id, $combustible_id);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();
        return $result ? true : false;
    }

    public function asignarCombustible($sucursal_id, $combustible_id)
    {
        return $this->asignarRelacion($sucursal_id, $combustible_id);
    }
}
