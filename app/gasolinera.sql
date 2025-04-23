-- Crear base de datos (opcional)
CREATE DATABASE IF NOT EXISTS gasolinera;
USE gasolinera;

-- Tabla: combustible
CREATE TABLE combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Definir AUTO_INCREMENT
  tipo VARCHAR(50)
);

-- Tabla: parametros_combustible
CREATE TABLE parametros_combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Definir AUTO_INCREMENT
  combustible_id INT,
  consumo_promedio_por_auto FLOAT NOT NULL,
  tiempo_promedio_carga TIME NOT NULL,
  largo_promedio_auto FLOAT NOT NULL,
  FOREIGN KEY (combustible_id) REFERENCES combustible(id)
);

-- Tabla: sucursal
CREATE TABLE sucursal (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Definir AUTO_INCREMENT
  nombre VARCHAR(100),
  ubicacion VARCHAR(255),
  bombas INT
);

-- Tabla: sucursal_combustible
CREATE TABLE sucursal_combustible (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Definir AUTO_INCREMENT
  sucursal_id INT,
  combustible_id INT,
  capacidad_actual FLOAT,
  estado TEXT,
  fecha_actualizada DATE,
  FOREIGN KEY (sucursal_id) REFERENCES sucursal(id),
  FOREIGN KEY (combustible_id) REFERENCES combustible(id)
);

-- Tabla: cola_estimada
CREATE TABLE cola_estimada (
  id INT AUTO_INCREMENT PRIMARY KEY,  -- Definir AUTO_INCREMENT
  sucursal_combustible_id INT,
  cant_autos INT,
  distancia_cola DECIMAL(10,0),
  tiempo_agotamiento TIME,
  fecha_actualizada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (sucursal_combustible_id) REFERENCES sucursal_combustible(id)
);