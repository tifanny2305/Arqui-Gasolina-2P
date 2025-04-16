<?php
class Database {
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = ''; // Cambia si tienes contraseña
    private $bd = 'gasolinera'; 
    public $conexion;

    public function __construct() {
        $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->bd);
        if ($this->conexion->connect_error) {
            die("Conexión fallida: " . $this->conexion->connect_error);
        }
    }

    public function obtenerConexion() {
        return $this->conexion;
    }
}
?>
