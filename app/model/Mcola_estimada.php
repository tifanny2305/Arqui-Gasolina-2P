<?php
require_once __DIR__ . '/../database.php';

class Sucursal
{
    private $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->obtenerConexion();
    }

    // CU1: Registrar Sucursal
    public function registrar($nombre, $ubicacion, $bombas)
    {
        $sql = "INSERT INTO sucursal (nombre, ubicacion, bombas)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $ubicacion, $bombas);
        return $stmt->execute();
    }

    // CU2: Listar Sucursales
    public function listar()
    {
        $sql = "SELECT * FROM sucursal";
        $resultado = $this->conexion->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // CU3: Consultar Detalle Sucursal
    public function obtenerPorId($id)
    {
        $sql = "SELECT * FROM sucursal WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
