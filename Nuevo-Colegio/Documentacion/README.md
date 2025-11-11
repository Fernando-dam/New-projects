# Sistema de Administración Escolar - Colegio Nuevos Horizontes

Sistema completo de gestión escolar con PHP, MySQL, JavaScript y CSS.

## 📋 Características

- ✅ Sistema de login con 3 roles (Administrador, Docente, Secretaria)
- ✅ Gestión completa de estudiantes
- ✅ Gestión de profesores
- ✅ Gestión de asignaturas
- ✅ Coordinación de horarios por asignatura
- ✅ Registro y consulta de notas
- ✅ Sistema de finanzas (gastos, ingresos, facturas)
- ✅ Estado de cuenta semanal
- ✅ Mensajería interna para administración y secretaria
- ✅ Base de datos completa con todas las relaciones
- ✅ Interfaz moderna y responsiva
- ✅ Operaciones CRUD completas (Crear, Leer, Actualizar, Eliminar)

## 🛠️ Requisitos del Sistema

- XAMPP (Apache + MySQL + PHP 7.4 o superior)
- Navegador web moderno (Chrome, Firefox, Edge, Safari)

## 📦 Instalación

### Paso 1: Instalar XAMPP
1. Descarga XAMPP desde: https://www.apachefriends.org/
2. Instala XAMPP en tu computadora
3. Inicia Apache y MySQL desde el panel de control de XAMPP

### Paso 2: Preparar los Archivos

Crea la siguiente estructura de carpetas dentro de `C:\xampp\htdocs\colegio\`:

```
colegio/
│
├── config.php
├── index.php
├── dashboard.php
├── estudiantes.php
├── profesores.php
├── asignaturas.php
├── horarios.php
├── notas.php
├── finanzas.php
├── mensajes.php
├── logout.php
│
├── includes/
│   ├── header.php
│   └── footer.php
│
├── css/
│   └── style.css
│
├── js/
│   ├── main.js
│   ├── estudiantes.js
│   ├── profesores.js
│   ├── asignaturas.js
│   ├── horarios.js
│   ├── notas.js
│   ├── finanzas.js
│   └── mensajes.js
│
└── database.sql
```

### Paso 3: Crear la Base de Datos

1. Abre tu navegador y ve a: `http://localhost/phpmyadmin`
2. Haz clic en "Nuevo" en el panel izquierdo
3. Crea una base de datos llamada `colegio_db`
4. Selecciona la base de datos `colegio_db`
5. Haz clic en la pestaña "SQL"
6. Copia todo el contenido del archivo `database.sql` y pégalo
7. Haz clic en "Continuar" para ejecutar el script

### Paso 4: Configurar la Aplicación

1. Abre el archivo `config.php`
2. Verifica que los datos de conexión sean correctos:
   - DB_HOST: `localhost`
   - DB_USER: `root`
   - DB_PASS: `` (vacío por defecto en XAMPP)
   - DB_NAME: `colegio_db`

### Paso 5: Acceder al Sistema

1. Abre tu navegador
2. Ve a: `http://localhost/colegio`
3. Usa las siguientes credenciales para acceder:

**Administrador:**
- Email: `admin@colegio.com`
- Contraseña: `password123`

**Secretaria:**
- Email: `secretaria@colegio.com`
- Contraseña: `password123`

## 📚 Módulos del Sistema

### 1. Dashboard
- Resumen estadístico del colegio
- Acceso rápido a todas las funciones
- Balance financiero mensual

### 2. Estudiantes
- Registrar nuevos estudiantes
- Ver lista completa de estudiantes
- Editar información de estudiantes
- Eliminar estudiantes (desactivar)

### 3. Profesores
- Registrar nuevos profesores
- Ver lista completa de profesores
- Editar información de profesores
- Eliminar profesores (desactivar)

### 4. Asignaturas
- Crear nuevas asignaturas
- Gestionar código y créditos
- Editar asignaturas existentes
- Eliminar asignaturas

### 5. Horarios
- Asignar profesores a asignaturas
- Configurar días y horas de clase
- Asignar aulas
- Eliminar horarios

### 6. Notas
- Seleccionar estudiante
- Registrar notas por asignatura
- Ver historial de notas por periodo
- Diferentes tipos de evaluación

### 7. Finanzas (Solo Admin y Secretaria)
- Registrar gastos del colegio
- Registrar ingresos
- Crear facturas para estudiantes
- Ver balance mensual
- Categorizar transacciones

### 8. Mensajería (Solo Admin y Secretaria)
- Enviar mensajes internos
- Ver mensajes recibidos
- Ver mensajes enviados
- Marcar mensajes como leídos

## 🔐 Seguridad

- Contraseñas hasheadas con bcrypt
- Sesiones seguras
- Protección contra inyección SQL con PDO preparado
- Sanitización de datos de entrada
- Control de acceso basado en roles

## 🎨 Diseño

- Interfaz moderna y profesional
- Diseño responsivo para móviles y tablets
- Colores corporativos
- Iconos intuitivos
- Alertas y notificaciones visuales

## 🐛 Solución de Problemas

### Error: "Could not connect to database"
- Verifica que MySQL esté ejecutándose en XAMPP
- Revisa las credenciales en `config.php`
- Asegúrate de que la base de datos `colegio_db` existe

### Error 404: Página no encontrada
- Verifica que los archivos estén en `C:\xampp\htdocs\colegio\`
- Asegúrate de que Apache esté ejecutándose
- Accede con la URL correcta: `http://localhost/colegio`

### Los estilos no cargan
- Verifica que la carpeta `css` contenga el archivo `style.css`
- Revisa la consola del navegador (F12) para ver errores
- Asegúrate de que las rutas sean relativas correctas

### Los modales no funcionan
- Verifica que todos los archivos JavaScript estén en la carpeta `js`
- Revisa la consola del navegador (F12) para ver errores
- Asegúrate de que los archivos JS se estén cargando correctamente

## 📝 Datos de Prueba

El sistema incluye datos de ejemplo:
- 2 profesores
- 3 asignaturas
- 2 estudiantes
- 2 usuarios (admin y secretaria)

## 🔄 Actualización de Contraseñas

Para cambiar contraseñas, genera un nuevo hash:

```php
<?php
echo password_hash('nueva_contraseña', PASSWORD_DEFAULT);
?>
```

Y actualiza en la base de datos:

```sql
UPDATE usuarios SET password = 'hash_generado' WHERE email = 'email@ejemplo.com';
```

## 👥 Roles y Permisos

- **Administrador**: Acceso completo a todo el sistema
- **Secretaria**: Acceso a estudiantes, profesores, finanzas y mensajes
- **Docente**: Acceso a estudiantes, asignaturas, horarios y notas

## 📞 Soporte

Para más información o soporte, contacta con el desarrollador.

## 📄 Licencia

Este proyecto es de código abierto y está disponible para uso educativo.

---

**Desarrollado con ❤️ para Colegio Nuevos Horizontes**