-- Base de datos para Sistema de Administración Escolar
CREATE DATABASE IF NOT EXISTS colegio_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE colegio_db;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'docente', 'secretaria') NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de estudiantes
CREATE TABLE estudiantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    documento VARCHAR(20) UNIQUE NOT NULL,
    direccion VARCHAR(200),
    telefono VARCHAR(20),
    email VARCHAR(100),
    fecha_ingreso DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de profesores
CREATE TABLE profesores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    documento VARCHAR(20) UNIQUE NOT NULL,
    especialidad VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    fecha_ingreso DATE NOT NULL,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de asignaturas
CREATE TABLE asignaturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    creditos INT DEFAULT 1,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de horarios
CREATE TABLE horarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asignatura_id INT NOT NULL,
    profesor_id INT NOT NULL,
    dia_semana ENUM('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    aula VARCHAR(20),
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id),
    FOREIGN KEY (profesor_id) REFERENCES profesores(id)
);

-- Tabla de inscripciones
CREATE TABLE inscripciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT NOT NULL,
    asignatura_id INT NOT NULL,
    fecha_inscripcion DATE NOT NULL,
    periodo VARCHAR(20) NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id),
    UNIQUE KEY (estudiante_id, asignatura_id, periodo)
);

-- Tabla de notas
CREATE TABLE notas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inscripcion_id INT NOT NULL,
    tipo_evaluacion VARCHAR(50) NOT NULL,
    nota DECIMAL(5,2) NOT NULL,
    fecha_registro DATE NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id)
);

-- Tabla de gastos
CREATE TABLE gastos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    categoria VARCHAR(50),
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de ingresos
CREATE TABLE ingresos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    descripcion VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    categoria VARCHAR(50),
    estudiante_id INT,
    usuario_id INT NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de facturas
CREATE TABLE facturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_factura VARCHAR(50) UNIQUE NOT NULL,
    estudiante_id INT NOT NULL,
    concepto VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_emision DATE NOT NULL,
    estado ENUM('pendiente', 'pagada', 'cancelada') DEFAULT 'pendiente',
    usuario_id INT NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de mensajes
CREATE TABLE mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remitente_id INT NOT NULL,
    destinatario_id INT NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id),
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id)
);

-- ============================================
-- INSERTAR USUARIOS CON LOS 3 ROLES
-- ============================================
-- Password para todos: password123

INSERT INTO usuarios (nombre, email, password, rol) VALUES 
('Administrador Principal', 'admin@colegio.com', '$2y$10$C.Zs9BJsdlkZokGZocn87.FUJkPru7/2SwJQ4xOv15EqJwiQ87chG', 'administrador'),
('Secretaria General', 'secretaria@colegio.com', '$2y$10$C.Zs9BJsdlkZokGZocn87.FUJkPru7/2SwJQ4xOv15EqJwiQ87chG', 'secretaria'),
('Profesor Juan García', 'docente@colegio.com', '$2y$10$C.Zs9BJsdlkZokGZocn87.FUJkPru7/2SwJQ4xOv15EqJwiQ87chG', 'docente');

-- ============================================
-- DATOS DE EJEMPLO
-- ============================================

-- Insertar profesores
INSERT INTO profesores (nombre, apellido, documento, especialidad, telefono, email, fecha_ingreso) VALUES
('Juan', 'García', '12345678', 'Matemáticas', '555-0001', 'juan.garcia@colegio.com', '2024-01-15'),
('María', 'López', '87654321', 'Lengua y Literatura', '555-0002', 'maria.lopez@colegio.com', '2024-01-15'),
('Pedro', 'Martínez', '11223344', 'Ciencias Naturales', '555-0003', 'pedro.martinez@colegio.com', '2024-02-01'),
('Ana', 'Rodríguez', '55667788', 'Historia', '555-0004', 'ana.rodriguez@colegio.com', '2024-02-01');

-- Insertar asignaturas
INSERT INTO asignaturas (nombre, descripcion, codigo, creditos) VALUES
('Matemáticas I', 'Álgebra y Geometría básica', 'MAT101', 4),
('Lengua Española', 'Gramática y Literatura', 'LEN101', 3),
('Ciencias Naturales', 'Biología y Física', 'CIE101', 4),
('Historia Universal', 'Historia de las civilizaciones', 'HIS101', 3),
('Inglés Básico', 'Inglés nivel principiante', 'ING101', 3);

-- Insertar estudiantes
INSERT INTO estudiantes (nombre, apellido, fecha_nacimiento, documento, direccion, telefono, email, fecha_ingreso) VALUES
('Carlos', 'Martínez', '2010-05-15', 'EST001', 'Calle Principal 123', '555-1001', 'carlos.m@email.com', '2024-09-01'),
('Ana', 'Rodríguez', '2010-08-22', 'EST002', 'Avenida Central 456', '555-1002', 'ana.r@email.com', '2024-09-01'),
('Luis', 'Fernández', '2010-03-10', 'EST003', 'Calle Secundaria 789', '555-1003', 'luis.f@email.com', '2024-09-01'),
('María', 'González', '2010-11-05', 'EST004', 'Avenida Norte 321', '555-1004', 'maria.g@email.com', '2024-09-01'),
('José', 'Pérez', '2010-07-18', 'EST005', 'Calle Sur 654', '555-1005', 'jose.p@email.com', '2024-09-01');

-- Insertar horarios de ejemplo
INSERT INTO horarios (asignatura_id, profesor_id, dia_semana, hora_inicio, hora_fin, aula) VALUES
(1, 1, 'lunes', '08:00:00', '10:00:00', 'A101'),
(1, 1, 'miercoles', '08:00:00', '10:00:00', 'A101'),
(2, 2, 'martes', '10:00:00', '12:00:00', 'A102'),
(2, 2, 'jueves', '10:00:00', '12:00:00', 'A102'),
(3, 3, 'lunes', '14:00:00', '16:00:00', 'LAB1'),
(3, 3, 'viernes', '14:00:00', '16:00:00', 'LAB1'),
(4, 4, 'miercoles', '14:00:00', '16:00:00', 'A103');

-- Insertar inscripciones de ejemplo
INSERT INTO inscripciones (estudiante_id, asignatura_id, fecha_inscripcion, periodo) VALUES
(1, 1, '2024-09-01', '2024-2'),
(1, 2, '2024-09-01', '2024-2'),
(1, 3, '2024-09-01', '2024-2'),
(2, 1, '2024-09-01', '2024-2'),
(2, 2, '2024-09-01', '2024-2'),
(2, 4, '2024-09-01', '2024-2'),
(3, 1, '2024-09-01', '2024-2'),
(3, 3, '2024-09-01', '2024-2'),
(4, 2, '2024-09-01', '2024-2'),
(4, 4, '2024-09-01', '2024-2'),
(5, 1, '2024-09-01', '2024-2'),
(5, 2, '2024-09-01', '2024-2'),
(5, 3, '2024-09-01', '2024-2');

-- Insertar notas de ejemplo
INSERT INTO notas (inscripcion_id, tipo_evaluacion, nota, fecha_registro, observaciones) VALUES
(1, 'Examen Parcial', 8.50, '2024-10-15', 'Buen desempeño'),
(1, 'Trabajo Práctico', 9.00, '2024-10-20', 'Excelente trabajo'),
(2, 'Examen Parcial', 7.50, '2024-10-15', 'Puede mejorar'),
(2, 'Participación', 8.00, '2024-10-25', 'Buena participación'),
(3, 'Examen Parcial', 9.50, '2024-10-15', 'Excelente comprensión'),
(4, 'Examen Parcial', 8.00, '2024-10-15', 'Buen trabajo'),
(5, 'Trabajo Práctico', 7.00, '2024-10-20', 'Necesita mejorar redacción');

-- Insertar gastos de ejemplo
INSERT INTO gastos (descripcion, monto, fecha, categoria, usuario_id) VALUES
('Pago de electricidad octubre', 250.00, '2024-10-05', 'Servicios', 1),
('Compra de materiales escolares', 180.50, '2024-10-10', 'Suministros', 1),
('Reparación de equipos', 320.00, '2024-10-12', 'Mantenimiento', 1),
('Pago de agua octubre', 85.00, '2024-10-15', 'Servicios', 2);

-- Insertar ingresos de ejemplo
INSERT INTO ingresos (descripcion, monto, fecha, categoria, estudiante_id, usuario_id) VALUES
('Matrícula Carlos Martínez', 500.00, '2024-09-01', 'Matrícula', 1, 2),
('Matrícula Ana Rodríguez', 500.00, '2024-09-01', 'Matrícula', 2, 2),
('Mensualidad octubre Carlos', 200.00, '2024-10-05', 'Mensualidad', 1, 2),
('Mensualidad octubre Ana', 200.00, '2024-10-05', 'Mensualidad', 2, 2),
('Mensualidad octubre Luis', 200.00, '2024-10-07', 'Mensualidad', 3, 2),
('Donación general', 1000.00, '2024-10-10', 'Donación', NULL, 1);

-- Insertar facturas de ejemplo
INSERT INTO facturas (numero_factura, estudiante_id, concepto, monto, fecha_emision, estado, usuario_id) VALUES
('FAC-20241001-0001', 1, 'Mensualidad Octubre 2024', 200.00, '2024-10-01', 'pagada', 2),
('FAC-20241001-0002', 2, 'Mensualidad Octubre 2024', 200.00, '2024-10-01', 'pagada', 2),
('FAC-20241001-0003', 3, 'Mensualidad Octubre 2024', 200.00, '2024-10-01', 'pagada', 2),
('FAC-20241001-0004', 4, 'Mensualidad Octubre 2024', 200.00, '2024-10-01', 'pendiente', 2),
('FAC-20241001-0005', 5, 'Mensualidad Octubre 2024', 200.00, '2024-10-01', 'pendiente', 2);

-- Insertar mensajes de ejemplo
INSERT INTO mensajes (remitente_id, destinatario_id, asunto, mensaje, leido) VALUES
(1, 2, 'Reunión de coordinación', 'Estimada secretaria, necesitamos coordinar una reunión para revisar los registros académicos. ¿Cuándo tiene disponibilidad?', FALSE),
(2, 1, 'RE: Reunión de coordinación', 'Buenos días, tengo disponibilidad el jueves en la tarde. ¿Le parece bien a las 3 PM?', TRUE),
(1, 3, 'Entrega de notas', 'Profesor García, por favor recuerde entregar las notas del último examen antes del viernes.', FALSE);