# 🎓 Asistente Virtual - Universidad X

Sistema completo de gestión de cursos con inscripciones, solicitudes, citas y mensajería.

## 📋 Requisitos Previos

- XAMPP (Apache + MySQL + PHP)
- Visual Studio Code (o cualquier editor de código)
- Navegador web moderno

## 📁 Estructura de Archivos

```
asistente_virtual/
│
├── config.php                  # Configuración y conexión a BD
├── index.php                   # Página principal
├── login.php                   # Inicio de sesión
├── register.php                # Registro de usuarios
├── dashboard.php               # Panel principal
├── logout.php                  # Cerrar sesión
│
├── cursos.php                  # Gestión de cursos (usuario)
├── solicitudes.php             # Solicitudes de documentos
├── citas.php                   # Gestión de citas
├── mensajes.php                # Sistema de mensajería
├── perfil.php                  # Perfil de usuario
├── verificacion.php            # Verificación de cuenta
├── ver_mensaje.php             # Ver mensaje individual
├── descargar_documento.php     # Descarga de documentos
│
├── admin_cursos.php            # Admin: Gestión de cursos
├── admin_solicitudes.php       # Admin: Gestión de solicitudes
├── admin_usuarios.php          # Admin: Gestión de usuarios
├── admin_citas.php             # Admin: Gestión de citas
│
├── css/
│   └── style.css               # Estilos CSS
│
├── js/
│   └── script.js               # JavaScript
│
├── includes/
│   ├── navbar.php              # Barra de navegación
│   └── sidebar.php             # Menú lateral
│
├── uploads/                    # Carpeta para documentos (crear manualmente)
│
└── database.sql                # Script de base de datos
```

## 🚀 Instalación Paso a Paso

### 1. Instalar XAMPP

1. Descarga XAMPP desde https://www.apachefriends.org/
2. Instala XAMPP en tu computadora
3. Inicia el Panel de Control de XAMPP

### 2. Configurar el Proyecto

1. Abre XAMPP Control Panel
2. Inicia Apache y MySQL
3. Ve a la carpeta de instalación de XAMPP (normalmente `C:\xampp\htdocs\`)
4. Crea una carpeta llamada `asistente_virtual`
5. Copia todos los archivos del proyecto en esta carpeta

### 3. Crear la Base de Datos

1. Abre tu navegador y ve a `http://localhost/phpmyadmin`
2. Haz clic en "Nuevo" en el menú lateral
3. Nombre de la base de datos: `asistente_virtual`
4. Haz clic en "Crear"
5. Selecciona la base de datos creada
6. Haz clic en la pestaña "SQL"
7. Copia y pega todo el contenido del archivo `database.sql`
8. Haz clic en "Continuar"

### 4. Configurar la Conexión

Verifica que el archivo `config.php` tenga esta configuración:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'asistente_virtual');
```

### 5. Crear Carpetas Necesarias

Dentro de la carpeta del proyecto, crea manualmente:
- Carpeta `uploads/` (para documentos subidos)

### 6. Acceder a la Aplicación

Abre tu navegador y ve a:
```
http://localhost/asistente_virtual
```

## 👤 Credenciales de Acceso

### Administrador
- **Email:** admin@universidad.com
- **Contraseña:** admin123

### Usuario de Prueba
Regístrate desde la página principal para crear tu cuenta de usuario.

## ✨ Funcionalidades

### Para Usuarios:
- ✅ Registro y autenticación
- 📚 Visualizar y inscribirse en cursos
- 📜 Solicitar certificados y constancias
- 📅 Agendar citas
- 💬 Enviar y recibir mensajes
- 🔐 Área de verificación de cuenta
- 👤 Gestión de perfil

### Para Administradores:
- 👥 Gestión de usuarios
- 📚 Gestión de cursos (crear, editar, eliminar)
- 📋 Aprobar/rechazar solicitudes de documentos
- 📅 Gestionar citas
- 💬 Sistema de mensajería

## 🔧 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo en XAMPP
- Comprueba las credenciales en `config.php`
- Asegúrate de haber importado el archivo `database.sql`

### Página en blanco
- Activa la visualización de errores en PHP
- Revisa los logs de Apache en `xampp/apache/logs/error.log`

### Errores de permisos
- En Windows: Da permisos de escritura a la carpeta `uploads/`
- Clic derecho en la carpeta → Propiedades → Seguridad

## 📱 Características Adicionales

- Diseño responsive (se adapta a móviles y tablets)
- Sistema de notificaciones en tiempo real
- Búsqueda y filtrado de información
- Exportación de datos
- Sistema de estadísticas

## 🔒 Seguridad

- Contraseñas encriptadas con `password_hash()`
- Protección contra SQL Injection
- Validación de sesiones
- Sanitización de datos de entrada

## 📧 Soporte

Para cualquier problema o consulta, contacta al administrador del sistema.

## 📄 Licencia

Este proyecto es de uso educativo para la Universidad X.

---

**Desarrollado con ❤️ para Universidad X**