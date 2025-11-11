# 🎓 Sistema de Asistente Virtual - Universidad X

## Descripción del Proyecto

Sistema completo de asistente virtual para la gestión de inscripciones de cursos de formación universitaria. La aplicación permite a los usuarios registrarse en cursos, solicitar certificados, agendar citas, y gestionar toda su información académica a través de una interfaz moderna y un chatbot inteligente.

## ✨ Características Principales

### Para Usuarios/Estudiantes
- 🔐 **Sistema de autenticación seguro** con JWT
- 📝 **Registro de cursos** con verificación de cupos
- 📜 **Solicitud de certificados y constancias**
- 📅 **Sistema de citas** para asesoría académica
- 💬 **Chat con asistente virtual** inteligente
- ✅ **Verificación de identidad** con documentos
- 📱 **Interfaz responsiva** y moderna
- 🔔 **Notificaciones en tiempo real**

### Para Administradores
- 👥 **Gestión completa de usuarios**
- 📊 **Reportes y estadísticas detalladas**
- 🗃️ **Administración de cursos**
- ✅ **Aprobación de verificaciones**
- 💾 **Exportación de datos**
- 🔍 **Búsqueda avanzada de usuarios**

## 🏗️ Arquitectura del Sistema

```
Frontend (HTML/CSS/JS)
         ↕
    API REST (Node.js/Express)
         ↕
    Base de Datos (MySQL)
         ↕
    Sistema de Archivos (Uploads)
```

## 📋 Requisitos del Sistema

### Requisitos Mínimos
- **Node.js**: >= 16.0.0
- **MySQL**: >= 8.0
- **NPM**: >= 8.0.0
- **Espacio en disco**: 2GB mínimo
- **RAM**: 4GB mínimo

### Recomendado para Producción
- **Node.js**: >= 18.0.0
- **MySQL**: >= 8.0.30
- **Redis**: >= 6.0 (para cache y sesiones)
- **Nginx**: Para proxy reverso
- **SSL**: Certificado válido

## 🚀 Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone https://github.com/universidad-x/asistente-virtual.git
cd asistente-virtual
```

### 2. Instalar Dependencias
```bash
npm install
```

### 3. Configurar Base de Datos
```bash
# Crear la base de datos
mysql -u root -p < database/schema.sql

# Ejecutar migraciones
npm run migrate

# Insertar datos de prueba
npm run seed
```

### 4. Configurar Variables de Entorno
```bash
# Copiar archivo de ejemplo
cp .env.example .env

# Editar variables según tu entorno
nano .env
```

### 5. Configuración de Directorios
```bash
# Crear directorios necesarios
mkdir -p uploads/documents
mkdir -p logs
chmod 755 uploads/documents
```

### 6. Iniciar el Servidor
```bash
# Desarrollo
npm run dev

# Producción
npm start
```

## 📁 Estructura del Proyecto

```
universidad-x-asistente-virtual/
├── 📄 server.js                 # Servidor principal
├── 📄 package.json             # Dependencias y scripts
├── 📄 .env                     # Variables de entorno
├── 📁 database/
│   ├── 📄 schema.sql           # Esquema de base de datos
│   ├── 📄 migrations/          # Migraciones
│   └── 📄 seeds/               # Datos de prueba
├── 📁 public/
│   ├── 📄 index.html           # Frontend principal
│   ├── 📄 styles.css           # Estilos
│   └── 📄 script.js            # JavaScript del frontend
├── 📁 routes/
│   ├── 📄 auth.js              # Rutas de autenticación
│   ├── 📄 courses.js           # Rutas de cursos
│   ├── 📄 certificates.js      # Rutas de certificados
│   └── 📄 admin.js             # Rutas administrativas
├── 📁 middleware/
│   ├── 📄 auth.js              # Middleware de autenticación
│   ├── 📄 validation.js        # Validaciones
│   └── 📄 upload.js            # Manejo de archivos
├── 📁 services/
│   ├── 📄 email.js             # Servicio de email
│   ├── 📄 pdf.js               # Generación de PDFs
│   └── 📄 chatbot.js           # Lógica del chatbot
├── 📁 utils/
│   ├── 📄 logger.js            # Sistema de logs
│   ├── 📄 helpers.js           # Funciones auxiliares
│   └── 📄 validators.js        # Validadores personalizados
├── 📁 uploads/
│   └── 📁 documents/           # Archivos subidos
├── 📁 logs/                    # Archivos de log
├── 📁 tests/                   # Pruebas unitarias
└── 📁 docs/                    # Documentación adicional
```

## 🔧 Configuración Detallada

### Variables de Entorno Principales

```env
# Servidor
PORT=3000
NODE_ENV=production

# Base de Datos
DB_HOST=localhost
DB_USER=app_universidad
DB_PASSWORD=UnivApp2025!
DB_NAME=universidad_x

# Seguridad
JWT_SECRET=tu_clave_secreta_super_segura
BCRYPT_SALT_ROUNDS=10

# Email
EMAIL_USER=sistema@universidad.edu
EMAIL_PASS=tu_contraseña_de_aplicación
```

### Configuración de Base de Datos

La base de datos incluye las siguientes tablas principales:

- **usuarios**: Información de usuarios y administradores
- **cursos**: Catálogo de cursos disponibles
- **inscripciones**: Registros de inscripciones
- **certificados**: Certificados y constancias
- **citas**: Sistema de citas y reuniones
- **mensajes**: Historial de chat
- **verificaciones**: Documentos de verificación
- **notificaciones**: Sistema de notificaciones

## 🔌 API Endpoints

### Autenticación
```http
POST /api/auth/login          # Iniciar sesión
POST /api/auth/register       # Registrar usuario
POST /api/auth/logout         # Cerrar sesión
```

### Cursos
```http
GET    /api/cursos            # Listar cursos
GET    /api/cursos/:id        # Detalles de curso
POST   /api/inscripciones     # Inscribirse en curso
GET    /api/inscripciones     # Mis inscripciones
```

### Certificados
```http
POST   /api/certificados      # Solicitar certificado
GET    /api/certificados      # Mis certificados
GET    /api/certificados/:id/download  # Descargar certificado
```

### Citas
```http
POST   /api/citas             # Solicitar cita
GET    /api/citas             # Mis citas
PUT    /api/citas/:id         # Modificar cita
```

### Mensajería
```http
POST   /api/mensajes          # Enviar mensaje
GET    /api/mensajes/:sesion  # Historial de chat
```

### Verificación
```http
POST   /api/verificacion/documento    # Subir documento
POST   /api/verificacion/codigo       # Verificar código
```

### Administración (Solo Admins)
```http
GET    /api/admin/usuarios            # Listar usuarios
GET    /api/admin/reportes/inscripciones  # Reporte de inscripciones
POST   /api/admin/cursos              # Crear curso
PUT    /api/admin/usuarios/:id        # Modificar usuario
```

## 👨‍💻 Uso del Sistema

### Para Estudiantes

1. **Registro e Inicio de Sesión**
   - Crear cuenta con email universitario
   - Verificar identidad subiendo documento
   - Iniciar sesión con credenciales

2. **Inscripción en Cursos**
   - Navegar catálogo de cursos
   - Ver detalles y requisitos
   - Inscribirse si hay cupos disponibles

3. **Gestión de Certificados**
   - Solicitar certificados de cursos completados
   - Descargar documentos en formato PDF
   - Verificar estado de solicitudes

4. **Sistema de Citas**
   - Agendar citas de asesoría académica
   - Seleccionar fecha y hora disponible
   - Recibir confirmación por email

5. **Chat con Asistente**
   - Hacer preguntas sobre el sistema
   - Obtener ayuda en tiempo real
   - Consultar información de cursos

### Para Administradores

1. **Gestión de Usuarios**
   - Ver lista completa de usuarios
   - Buscar y filtrar usuarios
   - Modificar información de cuentas
   - Aprobar verificaciones de identidad

2. **Administración de Cursos**
   - Crear nuevos cursos
   - Modificar información existente
   - Gestionar cupos e inscripciones
   - Ver estadísticas de participación

3. **Generación de Reportes**
   - Reportes de inscripciones por período
   - Estadísticas de certificados emitidos
   - Exportar datos en diferentes formatos
   - Análisis de uso del sistema

## 🧪 Testing

### Ejecutar Pruebas
```bash
# Todas las pruebas
npm test

# Pruebas en modo watch
npm run test:watch

# Pruebas con cobertura
npm run test:coverage
```

### Tipos de Pruebas Incluidas
- **Pruebas unitarias**: Funciones individuales
- **Pruebas de integración**: APIs y base de datos
- **Pruebas end-to-end**: Flujos completos de usuario

## 🚀 Despliegue en Producción

### Usando PM2
```bash
# Instalar PM2 globalmente
npm install -g pm2

# Iniciar aplicación
pm2 start ecosystem.config.js --env production

# Ver estado
pm2 status

# Ver logs
pm2 logs
```

### Con Docker
```bash
# Construir imagen
docker build -t universidad-x-app .

# Ejecutar contenedor
docker run -d -p 3000:3000 --env-file .env universidad-x-app
```

### Configuración de Nginx
```nginx
server {
    listen 80;
    server_name universidad.edu;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }
}
```

## 🔒 Seguridad

### Medidas Implementadas
- **Autenticación JWT** con expiración
- **Encriptación de contraseñas** con bcrypt
- **Rate limiting** para prevenir ataques
- **Validación de entrada** en todos los endpoints
- **Sanitización de datos** para prevenir XSS
- **Helmet.js** para headers de seguridad
- **CORS configurado** correctamente

### Mejores Prácticas
- Usar HTTPS en producción
- Actualizar dependencias regularmente
- Realizar auditorías de seguridad
- Implementar logs de seguridad
- Backup regular de base de datos

## 📊 Monitoreo y Logs

### Sistema de Logs
```javascript
// Niveles de log: error, warn, info, debug
logger.info('Usuario autenticado', { userId: 123 });
logger.error('Error en base de datos', error);
```

### Métricas Importantes
- Número de usuarios activos
- Inscripciones por día/mes
- Certificados generados
- Tiempo de respuesta de API
- Errores del sistema

## 🤝 Contribución

### Guía para Contribuir
1. Fork del repositorio
2. Crear rama feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### Estándares de Código
- Usar ESLint para linting
- Seguir convenciones de JavaScript
- Documentar funciones con JSDoc
- Escribir pruebas para nuevas funcionalidades
- Mantener cobertura de pruebas > 80%

## 📞 Soporte y Contacto

### Información de Contacto
- **Email de Soporte**: soporte@universidad.edu
- **Teléfono**: +1-800-UNIVERSIDAD
- **Documentación**: https://docs.universidad.edu
- **GitHub Issues**: https://github.com/universidad-x/asistente-virtual/issues

### Horarios de Soporte
- **Lunes a Viernes**: 8:00 AM - 6:00 PM
- **Fines de Semana**: Solo emergencias
- **Tiempo de Respuesta**: 24-48 horas

## 📝 Changelog

### v1.0.0 (2025-09-11)
- ✨ Sistema completo de asistente virtual
- 🔐 Autenticación y autorización
- 📝 Inscripciones en cursos
- 📜 Generación de certificados
- 📅 Sistema de citas
- 💬 Chat con IA
- ✅ Verificación de usuarios
- 👨‍💼 Panel administrativo

## 📄 Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE.md](LICENSE.md) para detalles.

## 🙏 Agradecimientos

- **Universidad X** por el apoyo al proyecto
- **Equipo de Desarrollo** por la implementación
- **Estudiantes** por el feedback y pruebas
- **Comunidad Open Source** por