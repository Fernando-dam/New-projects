-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_archivos;
USE sistema_archivos;

-- Crear tabla de archivos
CREATE TABLE IF NOT EXISTS archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_guardado VARCHAR(255) NOT NULL,
    ruta VARCHAR(500) NOT NULL,
    tipo_archivo VARCHAR(100) NOT NULL,
    tamano BIGINT NOT NULL,
    descripcion TEXT,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear índice para búsquedas más rápidas
CREATE INDEX idx_nombre ON archivos(nombre_original);
CREATE INDEX idx_fecha ON archivos(fecha_subida);
