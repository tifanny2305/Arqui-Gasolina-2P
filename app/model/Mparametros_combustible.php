<?php

require_once(__DIR__ . '/../config/database.php'); 

class Mparametros_combustible {
    private $db;
    
    public $combustible_id;
    public $consumo_promedio_por_auto;
    public $tiempo_promedio_carga;
    public $largo_promedio_auto;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->obtenerConexion();
    }

    // Método para obtener todos los parámetros de combustible
    public function obtenerParametros() {
        $query = "SELECT * FROM parametros_combustible";
        $result = $this->db->query($query);
        return $result;
    }

    // Método para crear un nuevo parámetro de combustible
    public function crearParametroCombustible($combustible_id, $consumo, $tiempo, $largo_promedio_auto) {
        $query = "INSERT INTO parametros_combustible (combustible_id, consumo_promedio_por_auto, tiempo_promedio_carga, largo_promedio_auto) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("isdd", $combustible_id, $consumo, $tiempo, $largo_promedio_auto);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;  // Retorna el ID del nuevo registro
        }
        
        return false;
    }

    // Método para obtener un parámetro de combustible por su ID
    public function obtenerParametroPorId($combustible_id) {
        $query = "SELECT * FROM parametros_combustible WHERE combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $combustible_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();  // Retorna un array asociativo con los datos
    }

    // Método para actualizar un parámetro de combustible
    public function actualizarParametro($combustible_id, $consumo, $tiempo, $largo_promedio_auto) {
        $query = "UPDATE parametros_combustible 
                  SET consumo_promedio_por_auto = ?, tiempo_promedio_carga = ?, largo_promedio_auto = ? 
                  WHERE combustible_id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssdi", $consumo, $tiempo, $largo_promedio_auto, $combustible_id);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

}
