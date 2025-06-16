-- Crear base de datos (opcional)
CREATE DATABASE IF NOT EXISTS gasolinera_patron;
USE gasolinera_patron;

-- Tabla: combustible
CREATE TABLE combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(50) NOT NULL
);

-- Tabla: parametros_combustible
CREATE TABLE parametros_combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,
  combustible_id INT NOT NULL,
  consumo_promedio_por_auto FLOAT NOT NULL,
  tiempo_promedio_carga TIME NOT NULL,
  largo_promedio_auto FLOAT NOT NULL,
  FOREIGN KEY (combustible_id) REFERENCES combustible(id) ON DELETE CASCADE,
  UNIQUE KEY unique_combustible_params (combustible_id)
);

-- Tabla: sucursal
CREATE TABLE sucursal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  ubicacion VARCHAR(255) NOT NULL,
  bombas INT NOT NULL
);

-- Tabla: sucursal_combustible (relación muchos a muchos)
CREATE TABLE sucursal_combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_id INT NOT NULL,
  combustible_id INT NOT NULL,
  FOREIGN KEY (sucursal_id) REFERENCES sucursal(id) ON DELETE CASCADE,
  FOREIGN KEY (combustible_id) REFERENCES combustible(id) ON DELETE CASCADE,
  UNIQUE KEY unique_sucursal_combustible (sucursal_id, combustible_id)
);

-- Tabla: almacenamiento (reemplaza capacidad_actual de sucursal_combustible)
CREATE TABLE almacenamiento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sucursal_combustible_id INT NOT NULL,
  cap_actual FLOAT NOT NULL DEFAULT 0,
  estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_combustible_id) REFERENCES sucursal_combustible(id) ON DELETE CASCADE,
  UNIQUE KEY unique_almacenamiento (sucursal_combustible_id)
);

-- Tabla: cola_estimada
CREATE TABLE cola_estimada (
  id INT AUTO_INCREMENT PRIMARY KEY,
  almacenamiento_id INT NOT NULL,
  cant_autos INT NOT NULL DEFAULT 0,
  distancia_cola DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  tiempo_agotamiento TIME,
  fecha_actualizada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (almacenamiento_id) REFERENCES almacenamiento(id) ON DELETE CASCADE,
  UNIQUE KEY unique_cola_almacenamiento (almacenamiento_id)
);