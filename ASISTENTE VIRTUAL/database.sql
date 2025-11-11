-- ================================================
-- ESQUEMA DE BASE DE DATOS CORREGIDO
-- Universidad X - Sistema de Inscripciones
-- ================================================

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS universidad_x;
USE universidad_x;

-- Eliminar tablas si existen (para reinstalación limpia)
DROP TABLE IF EXISTS auditoria;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS notificaciones;
DROP TABLE IF EXISTS verificaciones;
DROP TABLE IF EXISTS mensajes;
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS certificados;
DROP TABLE IF EXISTS inscripciones;
DROP TABLE IF EXISTS cursos;
DROP TABLE IF EXISTS usuarios;

-- ================================================
-- CREACIÓN DE TABLAS
-- ================================================

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nombre_completo VARCHAR(150) NOT NULL,
    tipo_usuario ENUM('estudiante', 'administrador', 'coordinador') DEFAULT 'estudiante',
    telefono VARCHAR(20),
    documento_identidad VARCHAR(20) UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    verificado BOOLEAN DEFAULT FALSE
);

-- Tabla de Cursos
CREATE TABLE cursos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_curso VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    duracion_horas INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    cupo_maximo INT DEFAULT 30,
    fecha_inicio DATE,
    fecha_fin DATE,
    modalidad ENUM('presencial', 'virtual', 'mixta') DEFAULT 'virtual',
    instructor VARCHAR(150),
    categoria VARCHAR(100),
    requisitos TEXT,
    estado ENUM('activo', 'inactivo', 'completo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Inscripciones
CREATE TABLE inscripciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    curso_id INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'confirmada', 'cancelada', 'completada') DEFAULT 'pendiente',
    forma_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'beca') DEFAULT 'efectivo',
    monto_pagado DECIMAL(10,2),
    fecha_pago TIMESTAMP NULL,
    observaciones TEXT,
    calificacion_final DECIMAL(5,2) NULL,
    certificado_emitido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_inscripcion (usuario_id, curso_id)
);

-- Tabla de Certificados y Constancias
CREATE TABLE certificados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inscripcion_id INT NOT NULL,
    tipo_documento ENUM('certificado', 'constancia', 'diploma', 'notas') NOT NULL,
    codigo_documento VARCHAR(50) UNIQUE NOT NULL,
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_emision TIMESTAMP NULL,
    estado ENUM('solicitado', 'en_proceso', 'listo', 'entregado') DEFAULT 'solicitado',
    archivo_url VARCHAR(500),
    observaciones TEXT,
    usuario_emisor INT,
    FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_emisor) REFERENCES usuarios(id)
);

-- Tabla de Citas
CREATE TABLE citas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo_cita ENUM('asesoria_academica', 'consulta_matricula', 'revision_certificados', 'orientacion_profesional') NOT NULL,
    fecha_solicitada DATE NOT NULL,
    hora_solicitada TIME NOT NULL,
    fecha_confirmada DATE NULL,
    hora_confirmada TIME NULL,
    estado ENUM('solicitada', 'confirmada', 'reprogramada', 'completada', 'cancelada') DEFAULT 'solicitada',
    motivo TEXT,
    observaciones TEXT,
    usuario_atiende INT NULL,
    modalidad ENUM('presencial', 'virtual') DEFAULT 'presencial',
    enlace_virtual VARCHAR(500),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_atiende) REFERENCES usuarios(id)
);

-- Tabla de Mensajes/Chat
CREATE TABLE mensajes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('usuario', 'asistente', 'administrador') NOT NULL,
    fecha_mensaje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    leido BOOLEAN DEFAULT FALSE,
    sesion_chat VARCHAR(100),
    respuesta_a INT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (respuesta_a) REFERENCES mensajes(id)
);

-- Tabla de Documentos de Verificación
CREATE TABLE verificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo_documento ENUM('cedula', 'pasaporte', 'licencia', 'titulo') NOT NULL,
    archivo_url VARCHAR(500) NOT NULL,
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    observaciones TEXT,
    codigo_verificacion VARCHAR(10),
    fecha_verificacion TIMESTAMP NULL,
    verificado_por INT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (verificado_por) REFERENCES usuarios(id)
);

-- Tabla de Notificaciones
CREATE TABLE notificaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    tipo ENUM('inscripcion', 'certificado', 'cita', 'pago', 'sistema') NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_lectura TIMESTAMP NULL,
    url_accion VARCHAR(500),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de Pagos
CREATE TABLE pagos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    inscripcion_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta_credito', 'tarjeta_debito', 'transferencia', 'paypal') NOT NULL,
    numero_transaccion VARCHAR(100),
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'completado', 'fallido', 'reembolsado') DEFAULT 'pendiente',
    observaciones TEXT,
    FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id) ON DELETE CASCADE
);

-- Tabla de Auditoría
CREATE TABLE auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50) NOT NULL,
    registro_id INT NOT NULL,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_usuario VARCHAR(45),
    user_agent TEXT,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ================================================
-- ÍNDICES PARA OPTIMIZACIÓN
-- ================================================

CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_tipo ON usuarios(tipo_usuario);
CREATE INDEX idx_usuarios_estado ON usuarios(estado);

CREATE INDEX idx_cursos_fecha_inicio ON cursos(fecha_inicio);
CREATE INDEX idx_cursos_categoria ON cursos(categoria);
CREATE INDEX idx_cursos_estado ON cursos(estado);

CREATE INDEX idx_inscripciones_usuario ON inscripciones(usuario_id);
CREATE INDEX idx_inscripciones_curso ON inscripciones(curso_id);
CREATE INDEX idx_inscripciones_estado ON inscripciones(estado);
CREATE INDEX idx_inscripciones_fecha ON inscripciones(fecha_inscripcion);

CREATE INDEX idx_certificados_inscripcion ON certificados(inscripcion_id);
CREATE INDEX idx_certificados_estado ON certificados(estado);
CREATE INDEX idx_certificados_tipo ON certificados(tipo_documento);

CREATE INDEX idx_citas_usuario ON citas(usuario_id);
CREATE INDEX idx_citas_fecha ON citas(fecha_solicitada);
CREATE INDEX idx_citas_estado ON citas(estado);

CREATE INDEX idx_mensajes_usuario ON mensajes(usuario_id);
CREATE INDEX idx_mensajes_sesion ON mensajes(sesion_chat);
CREATE INDEX idx_mensajes_fecha ON mensajes(fecha_mensaje);

-- ================================================
-- INSERTAR DATOS DE PRUEBA
-- ================================================

-- Insertar usuarios de prueba
INSERT INTO usuarios (username, password_hash, email, nombre_completo, tipo_usuario, documento_identidad, verificado) VALUES
('admin', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@universidad.edu', 'Administrador Sistema', 'administrador', 'ADM001', TRUE),
('estudiante', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'juan.perez@universidad.edu', 'Juan Pérez González', 'estudiante', '12345678A', TRUE),
('maria.garcia', '$2b$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'maria.garcia@universidad.edu', 'María García López', 'estudiante', '87654321B', FALSE);

-- Insertar cursos de prueba
INSERT INTO cursos (codigo_curso, nombre, descripcion, duracion_horas, precio, fecha_inicio, fecha_fin, modalidad, instructor, categoria) VALUES
('PROG001', 'Programación Web Avanzada', 'Curso completo de desarrollo web con tecnologías modernas', 120, 299.00, '2025-10-01', '2025-12-15', 'virtual', 'Prof. Ana Rodríguez', 'Programación'),
('DATA001', 'Ciencia de Datos', 'Introducción al análisis de datos con Python y R', 80, 399.00, '2025-10-15', '2025-12-01', 'virtual', 'Dr. Carlos Mendez', 'Datos'),
('CYBER001', 'Ciberseguridad', 'Fundamentos de seguridad informática', 60, 349.00, '2025-11-01', '2025-12-20', 'mixta', 'Ing. Laura Silva', 'Seguridad'),
('AI001', 'Inteligencia Artificial y ML', 'Machine Learning y Deep Learning aplicado', 100, 449.00, '2025-11-15', '2026-01-30', 'virtual', 'PhD. Roberto Kim', 'IA'),
('MOBILE001', 'Desarrollo Mobile', 'Apps para iOS y Android con React Native', 90, 299.00, '2025-10-20', '2025-12-30', 'virtual', 'Ing. Sofia Chen', 'Mobile');

-- Insertar inscripciones de prueba
INSERT INTO inscripciones (usuario_id, curso_id, estado, forma_pago, monto_pagado, calificacion_final, certificado_emitido) VALUES
(2, 1, 'completada', 'tarjeta', 299.00, 8.5, TRUE),
(2, 2, 'confirmada', 'transferencia', 399.00, NULL, FALSE),
(3, 1, 'confirmada', 'efectivo', 299.00, NULL, FALSE);

-- Insertar certificados de prueba
INSERT INTO certificados (inscripcion_id, tipo_documento, codigo_documento, estado, fecha_emision) VALUES
(1, 'certificado', 'CERT-PROG001-2025-001', 'listo', NOW());

-- Insertar citas de prueba
INSERT INTO citas (usuario_id, tipo_cita, fecha_solicitada, hora_solicitada, estado, motivo) VALUES
(2, 'asesoria_academica', '2025-09-18', '14:00:00', 'confirmada', 'Consulta sobre siguiente curso a tomar'),
(3, 'consulta_matricula', '2025-09-20', '10:30:00', 'solicitada', 'Información sobre proceso de matrícula');

-- Insertar mensajes de prueba
INSERT INTO mensajes (usuario_id, mensaje, tipo, sesion_chat) VALUES
(2, '¡Hola! Necesito información sobre los cursos disponibles', 'usuario', 'session_001'),
(2, '¡Hola! Te puedo ayudar con información sobre nuestros cursos. Tenemos 5 cursos disponibles en diferentes áreas.', 'asistente', 'session_001'),
(3, '¿Cómo puedo solicitar un certificado?', 'usuario', 'session_002'),
(3, 'Para solicitar un certificado, ve a la sección correspondiente y selecciona el curso completado.', 'asistente', 'session_002');

-- Insertar notificaciones de prueba
INSERT INTO notificaciones (usuario_id, titulo, mensaje, tipo) VALUES
(2, 'Certificado Disponible', 'Tu certificado del curso Programación Web está listo para descarga', 'certificado'),
(2, 'Cita Confirmada', 'Tu cita de asesoría académica ha sido confirmada para el 18/09/2025 a las 14:00', 'cita'),
(3, 'Inscripción Confirmada', 'Tu inscripción al curso Programación Web ha sido confirmada', 'inscripcion');

-- ================================================
-- MENSAJE DE CONFIRMACIÓN
-- ================================================
SELECT 'Base de datos creada exitosamente!' as mensaje;
SELECT COUNT(*) as total_usuarios FROM usuarios;
SELECT COUNT(*) as total_cursos FROM cursos;
SELECT COUNT(*) as total_inscripciones FROM inscripciones;