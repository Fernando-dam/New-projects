// ================================================
// SERVIDOR BACKEND - API REST
// Sistema Asistente Virtual - Universidad X
// ================================================

const express = require('express');
const mysql = require('mysql2/promise');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const cors = require('cors');
const multer = require('multer');
const path = require('path');
const fs = require('fs').promises;
const nodemailer = require('nodemailer');
const rateLimit = require('express-rate-limit');
const helmet = require('helmet');
require('dotenv').config();

// Configuración de la aplicación
const app = express();
const PORT = process.env.PORT || 3000;
const JWT_SECRET = process.env.JWT_SECRET || 'universidad_x_secret_2025';

// Middlewares de seguridad
app.use(helmet());
app.use(cors({
    origin: process.env.FRONTEND_URL || 'http://localhost:8080',
    credentials: true
}));

// Rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutos
    max: 100, // máximo 100 requests por ventana
    message: 'Demasiadas solicitudes, intente más tarde'
});
app.use(limiter);

// Middlewares de parseo
app.use(express.json({ limit: '10mb' }));
app.use(express.urlencoded({ extended: true }));

// Configuración de la base de datos
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'app_universidad',
    password: process.env.DB_PASSWORD || 'UnivApp2025!',
    database: process.env.DB_NAME || 'universidad_x',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
};

const db = mysql.createPool(dbConfig);

// Configuración de multer para subida de archivos
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        const uploadDir = './uploads/documents/';
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
        cb(null, file.fieldname + '-' + uniqueSuffix + path.extname(file.originalname));
    }
});

const upload = multer({
    storage: storage,
    limits: {
        fileSize: 5 * 1024 * 1024 // 5MB máximo
    },
    fileFilter: (req, file, cb) => {
        const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (allowedTypes.includes(file.mimetype)) {
            cb(null, true);
        } else {
            cb(new Error('Tipo de archivo no permitido'));
        }
    }
});

// Configuración del servicio de email
const emailTransporter = nodemailer.createTransporter({
    service: 'gmail',
    auth: {
        user: process.env.EMAIL_USER || 'sistema@universidad.edu',
        pass: process.env.EMAIL_PASS || 'password'
    }
});

// ================================================
// MIDDLEWARES DE AUTENTICACIÓN
// ================================================

const authenticateToken = async (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];

    if (!token) {
        return res.status(401).json({ error: 'Token de acceso requerido' });
    }

    try {
        const decoded = jwt.verify(token, JWT_SECRET);
        req.user = decoded;
        next();
    } catch (error) {
        return res.status(403).json({ error: 'Token inválido' });
    }
};

const requireAdmin = (req, res, next) => {
    if (req.user.tipo_usuario !== 'administrador') {
        return res.status(403).json({ error: 'Acceso solo para administradores' });
    }
    next();
};

// ================================================
// RUTAS DE AUTENTICACIÓN
// ================================================

// Login
app.post('/api/auth/login', async (req, res) => {
    try {
        const { username, password } = req.body;

        if (!username || !password) {
            return res.status(400).json({ error: 'Usuario y contraseña son requeridos' });
        }

        const [rows] = await db.execute(
            'SELECT * FROM usuarios WHERE username = ? AND estado = "activo"',
            [username]
        );

        if (rows.length === 0) {
            return res.status(401).json({ error: 'Credenciales inválidas' });
        }

        const user = rows[0];
        const passwordMatch = await bcrypt.compare(password, user.password_hash);

        if (!passwordMatch) {
            return res.status(401).json({ error: 'Credenciales inválidas' });
        }

        // Actualizar último acceso
        await db.execute(
            'UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?',
            [user.id]
        );

        // Generar JWT
        const token = jwt.sign(
            { 
                id: user.id, 
                username: user.username, 
                tipo_usuario: user.tipo_usuario,
                nombre_completo: user.nombre_completo
            },
            JWT_SECRET,
            { expiresIn: '8h' }
        );

        res.json({
            success: true,
            token,
            user: {
                id: user.id,
                username: user.username,
                nombre_completo: user.nombre_completo,
                email: user.email,
                tipo_usuario: user.tipo_usuario,
                verificado: user.verificado
            }
        });

    } catch (error) {
        console.error('Error en login:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Registro de nuevo usuario
app.post('/api/auth/register', async (req, res) => {
    try {
        const { username, password, email, nombre_completo, documento_identidad } = req.body;

        if (!username || !password || !email || !nombre_completo) {
            return res.status(400).json({ error: 'Todos los campos son requeridos' });
        }

        // Verificar si el usuario ya existe
        const [existingUser] = await db.execute(
            'SELECT id FROM usuarios WHERE username = ? OR email = ?',
            [username, email]
        );

        if (existingUser.length > 0) {
            return res.status(409).json({ error: 'Usuario o email ya existe' });
        }

        // Hashear la contraseña
        const saltRounds = 10;
        const passwordHash = await bcrypt.hash(password, saltRounds);

        // Insertar nuevo usuario
        const [result] = await db.execute(
            `INSERT INTO usuarios (username, password_hash, email, nombre_completo, documento_identidad, tipo_usuario)
             VALUES (?, ?, ?, ?, ?, 'estudiante')`,
            [username, passwordHash, email, nombre_completo, documento_identidad]
        );

        res.status(201).json({
            success: true,
            message: 'Usuario registrado correctamente',
            user_id: result.insertId
        });

    } catch (error) {
        console.error('Error en registro:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE CURSOS
// ================================================

// Obtener todos los cursos
app.get('/api/cursos', async (req, res) => {
    try {
        const [rows] = await db.execute(
            `SELECT c.*, 
                    (c.cupo_maximo - COALESCE(inscritos.total, 0)) as cupos_disponibles
             FROM cursos c
             LEFT JOIN (
                 SELECT curso_id, COUNT(*) as total 
                 FROM inscripciones 
                 WHERE estado = 'confirmada' 
                 GROUP BY curso_id
             ) inscritos ON c.id = inscritos.curso_id
             WHERE c.estado = 'activo'
             ORDER BY c.fecha_inicio`
        );

        res.json({
            success: true,
            cursos: rows
        });

    } catch (error) {
        console.error('Error al obtener cursos:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Obtener detalles de un curso específico
app.get('/api/cursos/:id', async (req, res) => {
    try {
        const { id } = req.params;

        const [rows] = await db.execute(
            `SELECT c.*, 
                    (c.cupo_maximo - COALESCE(inscritos.total, 0)) as cupos_disponibles,
                    COALESCE(inscritos.total, 0) as total_inscritos
             FROM cursos c
             LEFT JOIN (
                 SELECT curso_id, COUNT(*) as total 
                 FROM inscripciones 
                 WHERE estado = 'confirmada' 
                 GROUP BY curso_id
             ) inscritos ON c.id = inscritos.curso_id
             WHERE c.id = ?`,
            [id]
        );

        if (rows.length === 0) {
            return res.status(404).json({ error: 'Curso no encontrado' });
        }

        res.json({
            success: true,
            curso: rows[0]
        });

    } catch (error) {
        console.error('Error al obtener curso:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE INSCRIPCIONES
// ================================================

// Inscribir usuario en curso
app.post('/api/inscripciones', authenticateToken, async (req, res) => {
    try {
        const { curso_id, forma_pago } = req.body;
        const usuario_id = req.user.id;

        // Verificar si ya está inscrito
        const [existingInscription] = await db.execute(
            'SELECT id FROM inscripciones WHERE usuario_id = ? AND curso_id = ?',
            [usuario_id, curso_id]
        );

        if (existingInscription.length > 0) {
            return res.status(409).json({ error: 'Ya está inscrito en este curso' });
        }

        // Obtener información del curso
        const [courseInfo] = await db.execute(
            'SELECT nombre, precio, cupo_maximo FROM cursos WHERE id = ? AND estado = "activo"',
            [curso_id]
        );

        if (courseInfo.length === 0) {
            return res.status(404).json({ error: 'Curso no disponible' });
        }

        const curso = courseInfo[0];

        // Verificar cupos disponibles
        const [inscritosCount] = await db.execute(
            'SELECT COUNT(*) as total FROM inscripciones WHERE curso_id = ? AND estado = "confirmada"',
            [curso_id]
        );

        if (inscritosCount[0].total >= curso.cupo_maximo) {
            return res.status(409).json({ error: 'No hay cupos disponibles' });
        }

        // Insertar inscripción
        const [result] = await db.execute(
            `INSERT INTO inscripciones (usuario_id, curso_id, forma_pago, monto_pagado, estado)
             VALUES (?, ?, ?, ?, 'confirmada')`,
            [usuario_id, curso_id, forma_pago, curso.precio]
        );

        // Enviar email de confirmación
        await sendConfirmationEmail(req.user.email, req.user.nombre_completo, curso.nombre);

        res.status(201).json({
            success: true,
            message: 'Inscripción exitosa',
            inscripcion_id: result.insertId
        });

    } catch (error) {
        console.error('Error en inscripción:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Obtener inscripciones del usuario
app.get('/api/inscripciones', authenticateToken, async (req, res) => {
    try {
        const usuario_id = req.user.id;

        const [rows] = await db.execute(
            `SELECT i.*, c.nombre as nombre_curso, c.codigo_curso, c.fecha_inicio, c.fecha_fin
             FROM inscripciones i
             JOIN cursos c ON i.curso_id = c.id
             WHERE i.usuario_id = ?
             ORDER BY i.fecha_inscripcion DESC`,
            [usuario_id]
        );

        res.json({
            success: true,
            inscripciones: rows
        });

    } catch (error) {
        console.error('Error al obtener inscripciones:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE CERTIFICADOS
// ================================================

// Solicitar certificado
app.post('/api/certificados', authenticateToken, async (req, res) => {
    try {
        const { inscripcion_id, tipo_documento } = req.body;
        const usuario_id = req.user.id;

        // Verificar que la inscripción pertenece al usuario
        const [inscripcion] = await db.execute(
            `SELECT i.id, i.estado, c.nombre as nombre_curso
             FROM inscripciones i
             JOIN cursos c ON i.curso_id = c.id
             WHERE i.id = ? AND i.usuario_id = ? AND i.estado = 'completada'`,
            [inscripcion_id, usuario_id]
        );

        if (inscripcion.length === 0) {
            return res.status(404).json({ error: 'Inscripción no válida o curso no completado' });
        }

        // Generar código único para el certificado
        const codigoDocumento = `CERT-${tipo_documento.toUpperCase()}-${Date.now()}`;

        // Insertar solicitud de certificado
        const [result] = await db.execute(
            `INSERT INTO certificados (inscripcion_id, tipo_documento, codigo_documento, estado)
             VALUES (?, ?, ?, 'solicitado')`,
            [inscripcion_id, tipo_documento, codigoDocumento]
        );

        res.status(201).json({
            success: true,
            message: 'Solicitud de certificado enviada',
            codigo_documento: codigoDocumento,
            certificado_id: result.insertId
        });

    } catch (error) {
        console.error('Error al solicitar certificado:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Obtener certificados del usuario
app.get('/api/certificados', authenticateToken, async (req, res) => {
    try {
        const usuario_id = req.user.id;

        const [rows] = await db.execute(
            `SELECT cert.*, c.nombre as nombre_curso, c.codigo_curso
             FROM certificados cert
             JOIN inscripciones i ON cert.inscripcion_id = i.id
             JOIN cursos c ON i.curso_id = c.id
             WHERE i.usuario_id = ?
             ORDER BY cert.fecha_solicitud DESC`,
            [usuario_id]
        );

        res.json({
            success: true,
            certificados: rows
        });

    } catch (error) {
        console.error('Error al obtener certificados:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE CITAS
// ================================================

// Solicitar cita
app.post('/api/citas', authenticateToken, async (req, res) => {
    try {
        const { tipo_cita, fecha_solicitada, hora_solicitada, motivo, modalidad } = req.body;
        const usuario_id = req.user.id;

        // Validar fecha (no puede ser en el pasado)
        const fechaActual = new Date();
        const fechaSolicitud = new Date(fecha_solicitada);

        if (fechaSolicitud < fechaActual) {
            return res.status(400).json({ error: 'La fecha no puede ser en el pasado' });
        }

        // Insertar cita
        const [result] = await db.execute(
            `INSERT INTO citas (usuario_id, tipo_cita, fecha_solicitada, hora_solicitada, motivo, modalidad)
             VALUES (?, ?, ?, ?, ?, ?)`,
            [usuario_id, tipo_cita, fecha_solicitada, hora_solicitada, motivo, modalidad || 'presencial']
        );

        res.status(201).json({
            success: true,
            message: 'Cita solicitada correctamente',
            cita_id: result.insertId
        });

    } catch (error) {
        console.error('Error al solicitar cita:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Obtener citas del usuario
app.get('/api/citas', authenticateToken, async (req, res) => {
    try {
        const usuario_id = req.user.id;

        const [rows] = await db.execute(
            `SELECT citas.*, u_atiende.nombre_completo as nombre_atiende
             FROM citas
             LEFT JOIN usuarios u_atiende ON citas.usuario_atiende = u_atiende.id
             WHERE citas.usuario_id = ?
             ORDER BY citas.fecha_solicitada DESC`,
            [usuario_id]
        );

        res.json({
            success: true,
            citas: rows
        });

    } catch (error) {
        console.error('Error al obtener citas:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE MENSAJERÍA
// ================================================

// Enviar mensaje
app.post('/api/mensajes', authenticateToken, async (req, res) => {
    try {
        const { mensaje, sesion_chat } = req.body;
        const usuario_id = req.user.id;

        // Insertar mensaje del usuario
        const [result] = await db.execute(
            `INSERT INTO mensajes (usuario_id, mensaje, tipo, sesion_chat)
             VALUES (?, ?, 'usuario', ?)`,
            [usuario_id, mensaje, sesion_chat || generateSessionId()]
        );

        // Generar respuesta automática
        const respuestaAsistente = await generateAssistantResponse(mensaje);

        // Insertar respuesta del asistente
        await db.execute(
            `INSERT INTO mensajes (usuario_id, mensaje, tipo, sesion_chat, respuesta_a)
             VALUES (?, ?, 'asistente', ?, ?)`,
            [usuario_id, respuestaAsistente, sesion_chat || generateSessionId(), result.insertId]
        );

        res.json({
            success: true,
            mensaje_id: result.insertId,
            respuesta: respuestaAsistente
        });

    } catch (error) {
        console.error('Error al enviar mensaje:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Obtener historial de mensajes
app.get('/api/mensajes/:sesion_chat', authenticateToken, async (req, res) => {
    try {
        const { sesion_chat } = req.params;
        const usuario_id = req.user.id;

        const [rows] = await db.execute(
            `SELECT * FROM mensajes 
             WHERE usuario_id = ? AND sesion_chat = ?
             ORDER BY fecha_mensaje ASC`,
            [usuario_id, sesion_chat]
        );

        res.json({
            success: true,
            mensajes: rows
        });

    } catch (error) {
        console.error('Error al obtener mensajes:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE VERIFICACIÓN
// ================================================

// Subir documento de verificación
app.post('/api/verificacion/documento', authenticateToken, upload.single('documento'), async (req, res) => {
    try {
        if (!req.file) {
            return res.status(400).json({ error: 'No se ha enviado ningún archivo' });
        }

        const { tipo_documento } = req.body;
        const usuario_id = req.user.id;
        const archivo_url = `/uploads/documents/${req.file.filename}`;

        // Generar código de verificación
        const codigo_verificacion = Math.random().toString(36).substring(2, 8).toUpperCase();

        // Insertar registro de verificación
        const [result] = await db.execute(
            `INSERT INTO verificaciones (usuario_id, tipo_documento, archivo_url, codigo_verificacion)
             VALUES (?, ?, ?, ?)`,
            [usuario_id, tipo_documento, archivo_url, codigo_verificacion]
        );

        // Enviar código por email
        await sendVerificationCode(req.user.email, codigo_verificacion);

        res.json({
            success: true,
            message: 'Documento subido correctamente. Revise su email para el código de verificación.',
            verificacion_id: result.insertId
        });

    } catch (error) {
        console.error('Error al subir documento:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Verificar código
app.post('/api/verificacion/codigo', authenticateToken, async (req, res) => {
    try {
        const { codigo_verificacion } = req.body;
        const usuario_id = req.user.id;

        // Buscar verificación pendiente
        const [verification] = await db.execute(
            `SELECT * FROM verificaciones 
             WHERE usuario_id = ? AND codigo_verificacion = ? AND estado = 'pendiente'
             ORDER BY fecha_subida DESC LIMIT 1`,
            [usuario_id, codigo_verificacion]
        );

        if (verification.length === 0) {
            return res.status(400).json({ error: 'Código de verificación inválido' });
        }

        // Actualizar estado de verificación
        await db.execute(
            `UPDATE verificaciones SET estado = 'aprobado', fecha_verificacion = NOW()
             WHERE id = ?`,
            [verification[0].id]
        );

        // Marcar usuario como verificado
        await db.execute(
            `UPDATE usuarios SET verificado = TRUE WHERE id = ?`,
            [usuario_id]
        );

        res.json({
            success: true,
            message: 'Usuario verificado correctamente'
        });

    } catch (error) {
        console.error('Error en verificación:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE ADMINISTRACIÓN
// ================================================

// Búsqueda de usuarios (solo admin)
app.get('/api/admin/usuarios', authenticateToken, requireAdmin, async (req, res) => {
    try {
        const { search, page = 1, limit = 10 } = req.query;
        const offset = (page - 1) * limit;

        let query = `SELECT id, username, email, nombre_completo, tipo_usuario, fecha_registro, ultimo_acceso, estado, verificado
                     FROM usuarios`;
        let params = [];

        if (search) {
            query += ` WHERE nombre_completo LIKE ? OR email LIKE ? OR username LIKE ?`;
            params = [`%${search}%`, `%${search}%`, `%${search}%`];
        }

        query += ` ORDER BY fecha_registro DESC LIMIT ? OFFSET ?`;
        params.push(parseInt(limit), parseInt(offset));

        const [rows] = await db.execute(query, params);

        // Contar total de usuarios
        let countQuery = 'SELECT COUNT(*) as total FROM usuarios';
        let countParams = [];

        if (search) {
            countQuery += ` WHERE nombre_completo LIKE ? OR email LIKE ? OR username LIKE ?`;
            countParams = [`%${search}%`, `%${search}%`, `%${search}%`];
        }

        const [countResult] = await db.execute(countQuery, countParams);

        res.json({
            success: true,
            usuarios: rows,
            total: countResult[0].total,
            page: parseInt(page),
            limit: parseInt(limit)
        });

    } catch (error) {
        console.error('Error en búsqueda de usuarios:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Generar reporte de inscripciones (solo admin)
app.get('/api/admin/reportes/inscripciones', authenticateToken, requireAdmin, async (req, res) => {
    try {
        const { fecha_inicio, fecha_fin } = req.query;

        const [rows] = await db.execute(
            `CALL sp_reporte_inscripciones(?, ?)`,
            [fecha_inicio || '2025-01-01', fecha_fin || '2025-12-31']
        );

        res.json({
            success: true,
            reporte: rows[0]
        });

    } catch (error) {
        console.error('Error al generar reporte:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// FUNCIONES AUXILIARES
// ================================================

function generateSessionId() {
    return 'session_' + Date.now() + '_' + Math.random().toString(36).substring(2);
}

async function generateAssistantResponse(mensaje) {
    const mensajeLower = mensaje.toLowerCase();
    
    // Respuestas basadas en palabras clave
    if (mensajeLower.includes('curso') || mensajeLower.includes('inscrib')) {
        return 'Para inscribirte en un curso, puedes ver la lista de cursos disponibles y seleccionar el que más te interese. ¿Hay algún curso específico sobre el que quieres más información?';
    } else if (mensajeLower.includes('certificado') || mensajeLower.includes('constancia')) {
        return 'Puedes solicitar certificados una vez que hayas completado un curso. Ve a la sección de certificados para hacer tu solicitud. ¿Para qué curso necesitas el certificado?';
    } else if (mensajeLower.includes('cita') || mensajeLower.includes('reunion')) {
        return 'Para agendar una cita, puedes usar nuestro sistema de citas. Ofrecemos asesoría académica, consultas de matrícula y más. ¿Qué tipo de cita necesitas?';
    } else if (mensajeLower.includes('ayuda') || mensajeLower.includes('help')) {
        return 'Estoy aquí para ayudarte con inscripciones, certificados, citas y más. ¿En qué específicamente puedo asistirte?';
    } else if (mensajeLower.includes('precio') || mensajeLower.includes('costo')) {
        return 'Los precios de nuestros cursos varían. Puedes consultar la información específica de cada curso en nuestra lista. ¿Te interesa algún curso en particular?';
    } else {
        return 'Gracias por tu mensaje. ¿Podrías ser más específico sobre en qué puedo ayudarte? Puedo asistirte con inscripciones, certificados, citas y más información académica.';
    }
}

async function sendConfirmationEmail(email, nombre, nombreCurso) {
    const mailOptions = {
        from: process.env.EMAIL_USER,
        to: email,
        subject: 'Confirmación de Inscripción - Universidad X',
        html: `
            <h2>¡Inscripción Confirmada!</h2>
            <p>Estimado/a ${nombre},</p>
            <p>Tu inscripción al curso <strong>${nombreCurso}</strong> ha sido confirmada exitosamente.</p>
            <p>Próximamente recibirás información detallada sobre:</p>
            <ul>
                <li>Fechas y horarios de clases</li>
                <li>Materiales de estudio</li>
                <li>Acceso a la plataforma virtual</li>
                <li>Información del instructor</li>
            </ul>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
            <p>¡Bienvenido a Universidad X!</p>
            <hr>
            <small>Este es un email automático, por favor no responder.</small>
        `
    };

    try {
        await emailTransporter.sendMail(mailOptions);
        console.log('Email de confirmación enviado a:', email);
    } catch (error) {
        console.error('Error al enviar email:', error);
    }
}

async function sendVerificationCode(email, codigo) {
    const mailOptions = {
        from: process.env.EMAIL_USER,
        to: email,
        subject: 'Código de Verificación - Universidad X',
        html: `
            <h2>Código de Verificación</h2>
            <p>Tu código de verificación es: <strong>${codigo}</strong></p>
            <p>Este código es válido por 24 horas.</p>
            <p>Si no solicitaste esta verificación, ignora este mensaje.</p>
            <hr>
            <small>Universidad X - Sistema de Seguridad</small>
        `
    };

    try {
        await emailTransporter.sendMail(mailOptions);
        console.log('Código de verificación enviado a:', email);
    } catch (error) {
        console.error('Error al enviar código:', error);
    }
}

// ================================================
// RUTAS DE NOTIFICACIONES
// ================================================

// Obtener notificaciones del usuario
app.get('/api/notificaciones', authenticateToken, async (req, res) => {
    try {
        const usuario_id = req.user.id;

        const [rows] = await db.execute(
            `SELECT * FROM notificaciones 
             WHERE usuario_id = ? 
             ORDER BY fecha_creacion DESC 
             LIMIT 20`,
            [usuario_id]
        );

        res.json({
            success: true,
            notificaciones: rows
        });

    } catch (error) {
        console.error('Error al obtener notificaciones:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// Marcar notificación como leída
app.put('/api/notificaciones/:id/leer', authenticateToken, async (req, res) => {
    try {
        const { id } = req.params;
        const usuario_id = req.user.id;

        await db.execute(
            `UPDATE notificaciones 
             SET leida = TRUE, fecha_lectura = NOW() 
             WHERE id = ? AND usuario_id = ?`,
            [id, usuario_id]
        );

        res.json({
            success: true,
            message: 'Notificación marcada como leída'
        });

    } catch (error) {
        console.error('Error al marcar notificación:', error);
        res.status(500).json({ error: 'Error interno del servidor' });
    }
});

// ================================================
// RUTAS DE ARCHIVOS ESTÁTICOS
// ================================================

// Servir archivos de documentos (con autenticación)
app.get('/uploads/documents/:filename', authenticateToken, (req, res) => {
    const filename = req.params.filename;
    const filepath = path.join(__dirname, 'uploads', 'documents', filename);
    
    // Verificar que el archivo existe
    if (fs.existsSync(filepath)) {
        res.sendFile(filepath);
    } else {
        res.status(404).json({ error: 'Archivo no encontrado' });
    }
});

// ================================================
// MIDDLEWARE DE MANEJO DE ERRORES
// ================================================

app.use((error, req, res, next) => {
    console.error('Error no manejado:', error);
    
    if (error instanceof multer.MulterError) {
        if (error.code === 'LIMIT_FILE_SIZE') {
            return res.status(400).json({ error: 'Archivo demasiado grande (máximo 5MB)' });
        }
    }
    
    res.status(500).json({ error: 'Error interno del servidor' });
});

// Middleware para rutas no encontradas
app.use('*', (req, res) => {
    res.status(404).json({ error: 'Ruta no encontrada' });
});

// ================================================
// INICIALIZACIÓN DEL SERVIDOR
// ================================================

async function startServer() {
    try {
        // Crear directorio de uploads si no existe
        const uploadsDir = './uploads/documents';
        await fs.mkdir(uploadsDir, { recursive: true });
        
        // Verificar conexión a la base de datos
        const connection = await db.getConnection();
        console.log('✅ Conexión a la base de datos establecida');
        connection.release();
        
        // Iniciar servidor
        app.listen(PORT, () => {
            console.log(`🚀 Servidor ejecutándose en puerto ${PORT}`);
            console.log(`📱 API disponible en: http://localhost:${PORT}/api`);
            console.log(`🔒 Usando JWT Secret: ${JWT_SECRET.substring(0, 10)}...`);
        });
        
    } catch (error) {
        console.error('❌ Error al iniciar servidor:', error);
        process.exit(1);
    }
}

// Manejo de cierre graceful
process.on('SIGINT', async () => {
    console.log('\n🔄 Cerrando servidor...');
    await db.end();
    console.log('✅ Servidor cerrado correctamente');
    process.exit(0);
});

// Iniciar el servidor
startServer();