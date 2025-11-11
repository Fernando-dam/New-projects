-- Crear base de datos
CREATE DATABASE IF NOT EXISTS colegio_nuevos_horizontes;
USE colegio_nuevos_horizontes;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'docente', 'secretaria') NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de estudiantes
CREATE TABLE estudiantes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(200) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    fecha_nacimiento DATE,
    genero ENUM('M', 'F'),
    grado INT NOT NULL,
    seccion VARCHAR(10),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    nombre_tutor VARCHAR(200),
    telefono_tutor VARCHAR(20),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de profesores
CREATE TABLE profesores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(200) NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,
    especialidad VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de asignaturas
CREATE TABLE asignaturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    grado INT NOT NULL,
    estado ENUM('activa', 'inactiva') DEFAULT 'activa'
);

-- Tabla de horarios
CREATE TABLE horarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    profesor_id INT,
    asignatura_id INT,
    grado INT,
    seccion VARCHAR(10),
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'),
    hora_inicio TIME,
    hora_fin TIME,
    aula VARCHAR(20),
    FOREIGN KEY (profesor_id) REFERENCES profesores(id),
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id)
);

-- Tabla de calificaciones
CREATE TABLE calificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT,
    asignatura_id INT,
    periodo ENUM('1', '2', '3', '4') NOT NULL,
    nota1 DECIMAL(4,2),
    nota2 DECIMAL(4,2),
    nota3 DECIMAL(4,2),
    nota_final DECIMAL(4,2),
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id),
    FOREIGN KEY (asignatura_id) REFERENCES asignaturas(id)
);

-- Tabla de facturas
CREATE TABLE facturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estudiante_id INT,
    numero_factura VARCHAR(50) UNIQUE NOT NULL,
    concepto VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_emision DATE,
    fecha_vencimiento DATE,
    estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente',
    metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta'),
    fecha_pago DATE,
    observaciones TEXT,
    FOREIGN KEY (estudiante_id) REFERENCES estudiantes(id)
);

-- Tabla de finanzas
CREATE TABLE finanzas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    concepto VARCHAR(200) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE,
    categoria VARCHAR(100),
    descripcion TEXT,
    factura_id INT,
    FOREIGN KEY (factura_id) REFERENCES facturas(id)
);

-- Tabla de mensajes
CREATE TABLE mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    remitente_id INT,
    destinatario_id INT,
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (remitente_id) REFERENCES usuarios(id),
    FOREIGN KEY (destinatario_id) REFERENCES usuarios(id)
);

-- Insertar datos de prueba

-- Usuarios
INSERT INTO usuarios (usuario, password, nombre, rol, email) VALUES
('admin', 'admin123', 'Administrador Principal', 'admin', 'admin@colegio.edu'),
('profesor1', 'prof123', 'Juan Pérez', 'docente', 'juan.perez@colegio.edu'),
('secretaria', 'secre123', 'María García', 'secretaria', 'maria.garcia@colegio.edu');

-- Estudiantes de prueba
INSERT INTO estudiantes (nombre_completo, cedula, fecha_nacimiento, genero, grado, seccion, telefono, email) VALUES
('Ana María Rodríguez', '001-1234567-8', '2010-05-15', 'F', 7, 'A', '809-123-4567', 'ana.rodriguez@email.com'),
('Carlos José Martínez', '001-2345678-9', '2009-08-22', 'M', 8, 'B', '809-234-5678', 'carlos.martinez@email.com'),
('Laura Michelle Sánchez', '001-3456789-0', '2011-02-10', 'F', 6, 'A', '809-345-6789', 'laura.sanchez@email.com');

-- Profesores de prueba
INSERT INTO profesores (nombre_completo, cedula, especialidad, telefono, email) VALUES
('Roberto Carlos Jiménez', '002-1234567-8', 'Matemáticas', '809-456-7890', 'roberto.jimenez@colegio.edu'),
('Sandra Patricia López', '002-2345678-9', 'Ciencias Sociales', '809-567-8901', 'sandra.lopez@colegio.edu');

-- Asignaturas de prueba
INSERT INTO asignaturas (nombre, descripcion, grado) VALUES
('Matemáticas', 'Álgebra, geometría y cálculo', 7),
('Lengua Española', 'Gramática y literatura', 7),
('Ciencias Naturales', 'Biología, física y química', 7),
('Ciencias Sociales', 'Historia y geografía', 7);

-- Horarios de prueba
INSERT INTO horarios (profesor_id, asignatura_id, grado, seccion, dia_semana, hora_inicio, hora_fin, aula) VALUES
(1, 1, 7, 'A', 'Lunes', '08:00:00', '09:00:00', 'Aula 101'),
(1, 1, 7, 'A', 'Miércoles', '08:00:00', '09:00:00', 'Aula 101'),
(2, 4, 7, 'A', 'Martes', '10:00:00', '11:00:00', 'Aula 102');

-- Facturas de prueba
INSERT INTO facturas (estudiante_id, numero_factura, concepto, monto, fecha_emision, fecha_vencimiento) VALUES
(1, 'FAC-2023-001', 'Mensualidad Enero 2023', 2500.00, '2023-01-01', '2023-01-15'),
(2, 'FAC-2023-002', 'Mensualidad Enero 2023', 2500.00, '2023-01-01', '2023-01-15');

-- Finanzas de prueba
INSERT INTO finanzas (tipo, concepto, monto, fecha, categoria) VALUES
('ingreso', 'Mensualidad Ana Rodríguez', 2500.00, '2023-01-10', 'matrículas'),
('gasto', 'Materiales de oficina', 500.00, '2023-01-05', 'suministros');