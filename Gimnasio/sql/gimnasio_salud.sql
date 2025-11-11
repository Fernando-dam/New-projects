-- Base de datos para Gimnasio Salud
-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS gimnasio_salud CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE gimnasio_salud;

-- Tabla de contactos
CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE,
    INDEX idx_fecha (fecha_creacion),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios (para futuras implementaciones)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    fecha_nacimiento DATE,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de cálculos IMC (para histórico de usuarios)
CREATE TABLE IF NOT EXISTS calculos_imc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    peso DECIMAL(5,2) NOT NULL,
    altura DECIMAL(5,2) NOT NULL,
    imc DECIMAL(5,2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    fecha_calculo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_calculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de planes/membresías
CREATE TABLE IF NOT EXISTS planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    duracion_meses INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de suscripciones newsletter
CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    fecha_suscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar planes de ejemplo
INSERT INTO planes (nombre, descripcion, precio, duracion_meses) VALUES
('Plan Básico', 'Acceso al gimnasio en horario completo', 29.99, 1),
('Plan Premium', 'Acceso al gimnasio + clases grupales', 49.99, 1),
('Plan Elite', 'Acceso completo + entrenador personal', 89.99, 1),
('Plan Anual Básico', 'Plan básico con descuento anual', 299.99, 12),
('Plan Anual Premium', 'Plan premium con descuento anual', 499.99, 12),
('Plan Anual Elite', 'Plan elite con descuento anual', 899.99, 12);

-- Insertar algunos contactos de ejemplo (opcional)
INSERT INTO contactos (nombre, email, telefono, asunto, mensaje) VALUES
('Juan Pérez', 'juan@email.com', '123456789', 'Información sobre planes', 'Me gustaría información sobre los planes disponibles'),
('María García', 'maria@email.com', '987654321', 'Clases de yoga', '¿Tienen clases de yoga para principiantes?'),
('Carlos López', 'carlos@email.com', '555555555', 'Horarios', '¿Cuál es el horario de apertura del gimnasio?');

-- Vista para estadísticas de contactos
CREATE VIEW vista_contactos_recientes AS
SELECT 
    id,
    nombre,
    email,
    asunto,
    DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha,
    leido
FROM contactos
ORDER BY fecha_creacion DESC
LIMIT 100;

-- Procedimiento almacenado para obtener estadísticas de IMC
DELIMITER //
CREATE PROCEDURE obtener_estadisticas_imc()
BEGIN
    SELECT 
        categoria,
        COUNT(*) as total,
        AVG(imc) as imc_promedio,
        MIN(fecha_calculo) as primera_medicion,
        MAX(fecha_calculo) as ultima_medicion
    FROM calculos_imc
    GROUP BY categoria
    ORDER BY total DESC;
END //
DELIMITER ;