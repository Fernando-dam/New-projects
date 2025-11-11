# YouTube Clone - Aplicación Web Completa

## 📋 Descripción
Clon completo de YouTube con todas las funcionalidades principales incluyendo:
- ✅ Subir videos
- ✅ Ver videos
- ✅ Dar me gusta / No me gusta
- ✅ Comentar
- ✅ Suscribirse a canales
- ✅ Crear y personalizar canales
- ✅ Búsqueda de videos
- ✅ Historial de reproducción
- ✅ Videos relacionados

## 🛠️ Requisitos
- XAMPP (PHP 7.4 o superior, MySQL)
- Visual Studio Code (o cualquier editor de código)
- Navegador web moderno

## 📦 Instalación

### 1. Instalar XAMPP
1. Descarga XAMPP desde: https://www.apachefriends.org/
2. Instala XAMPP en tu computadora
3. Abre el Panel de Control de XAMPP

### 2. Configurar el Proyecto

1. **Copia los archivos del proyecto** a la carpeta `htdocs` de XAMPP:
   ```
   C:\xampp\htdocs\youtube_clone\
   ```

2. **Estructura de carpetas** (crear las siguientes carpetas si no existen):
   ```
   youtube_clone/
   ├── api/
   │   ├── like.php
   │   ├── subscribe.php
   │   └── comment.php
   ├── assets/
   │   ├── css/
   │   │   └── style.css
   │   ├── js/
   │   │   ├── main.js
   │   │   └── video.js
   │   └── images/
   │       └── default-avatar.png
   ├── config/
   │   └── database.php
   ├── includes/
   │   ├── header.php
   │   └── sidebar.php
   ├── uploads/
   │   ├── videos/
   │   └── thumbnails/
   ├── index.php
   ├── watch.php
   ├── upload.php
   ├── login.php
   ├── register.php
   ├── channel.php
   ├── search.php
   ├── logout.php
   └── database.sql
   ```

3. **Crea una imagen por defecto para avatares**:
   - Guarda una imagen llamada `default-avatar.png` en `assets/images/`
   - Tamaño recomendado: 250x250 px

### 3. Configurar la Base de Datos

1. **Inicia los servicios en XAMPP**:
   - Abre el Panel de Control de XAMPP
   - Haz clic en "Start" en Apache
   - Haz clic en "Start" en MySQL

2. **Accede a phpMyAdmin**:
   - Abre tu navegador
   - Ve a: http://localhost/phpmyadmin

3. **Crea la base de datos**:
   - Haz clic en "Nueva" (New) en el menú izquierdo
   - Copia y pega el contenido del archivo `database.sql`
   - Haz clic en "Continuar" (Go)

### 4. Configurar la Conexión

Verifica que el archivo `config/database.php` tenga estos valores:
```php
private $host = "localhost";
private $db_name = "youtube_clone";
private $username = "root";
private $password = ""; // Vacío por defecto en XAMPP
```

### 5. Configurar Permisos

Asegúrate de que las carpetas `uploads/` tengan permisos de escritura:
- Windows: Clic derecho > Propiedades > Desmarcar "Solo lectura"
- Linux/Mac: `chmod -R 777 uploads/`

## 🚀 Ejecutar la Aplicación

1. Asegúrate de que Apache y MySQL estén corriendo en XAMPP

2. Abre tu navegador y ve a:
   ```
   http://localhost/youtube_clone/
   ```

3. **Crear una cuenta**:
   - Haz clic en "Iniciar sesión"
   - Luego en "Regístrate aquí"
   - Completa el formulario de registro

4. **Subir tu primer video**:
   - Inicia sesión
   - Haz clic en el icono de cámara en el header
   - O ve a: http://localhost/youtube_clone/upload.php
   - Selecciona un video y una miniatura
   - Completa el formulario y haz clic en "Subir Video"

## 🎯 Funcionalidades Principales

### Usuario
- **Registro e inicio de sesión** seguro con contraseñas encriptadas
- **Perfil de usuario** personalizable
- **Historial de visualización** automático

### Videos
- **Subir videos** con miniatura personalizada
- **Reproductor de video** HTML5 con controles completos
- **Contador de vistas** automático
- **Categorización** de videos

### Interacciones
- **Me gusta / No me gusta** en videos
- **Comentarios** con sistema de respuestas
- **Compartir** videos
- **Guardar** en listas de reproducción

### Canales
- **Crear canal** personalizado
- **Banner y avatar** del canal
- **Suscripciones** a otros canales
- **Contador de suscriptores**
- **Feed de suscripciones**

### Búsqueda
- **Motor de búsqueda** en títulos, descripciones y etiquetas
- **Filtros** por categoría
- **Videos relacionados** basados en contenido

## 🔧 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL esté corriendo en XAMPP
- Comprueba las credenciales en `config/database.php`
- Asegúrate de haber importado el archivo `database.sql`

### No se pueden subir videos
- Verifica los permisos de la carpeta `uploads/`
- Revisa el límite de tamaño de archivo en `php.ini`:
  ```
  upload_max_filesize = 500M
  post_max_size = 500M
  max_execution_time = 300
  ```
- Reinicia Apache después de cambiar php.ini

### Los estilos no se cargan
- Verifica la ruta de los archivos CSS
- Limpia la caché del navegador (Ctrl + F5)
- Comprueba la consola del navegador para errores

### Las funciones de like/comentarios no funcionan
- Verifica que estés conectado (inicia sesión)
- Comprueba la consola del navegador para errores JavaScript
- Asegúrate de que los archivos en la carpeta `api/` existan

## 📱 Características Responsive

La aplicación es completamente responsive y funciona en:
- 💻 Desktop (1920px+)
- 💻 Laptop (1024px - 1919px)
- 📱 Tablet (768px - 1023px)
- 📱 Mobile (320px - 767px)

## 🎨 Personalización

### Cambiar colores
Edita las variables CSS en `assets/css/style.css`:
```css
:root {
    --primary-color: #ff0000;
    --bg-color: #0f0f0f;
    --secondary-bg: #212121;
    --hover-bg: #3f3f3f;
    --text-primary: #ffffff;
    --text-secondary: #aaaaaa;
}
```

### Agregar nuevas categorías
Edita el archivo `upload.php` en la sección de categorías.

## 🔐 Seguridad

- ✅ Contraseñas encriptadas con `password_hash()`
- ✅ Protección contra SQL Injection con prepared statements
- ✅ Validación de sesiones
- ✅ Escape de HTML para prevenir XSS
- ✅ Validación de tipos de archivo en uploads

## 📈 Próximas Mejoras

- [ ] Sistema de notificaciones en tiempo real
- [ ] Chat en vivo durante transmisiones
- [ ] Edición de videos en el navegador
- [ ] Monetización y ads
- [ ] Sistema de reportes
- [ ] Moderación de contenido
- [ ] API REST completa
- [ ] Aplicación móvil nativa

## 🐛 Reportar Problemas

Si encuentras algún error o tienes sugerencias, puedes:
1. Revisar la consola del navegador (F12)
2. Verificar los logs de Apache en XAMPP
3. Revisar los errores de MySQL en phpMyAdmin

## 📄 Licencia

Este proyecto es de código abierto y está disponible para uso educativo.

## 👨‍💻 Desarrollo

Desarrollado con:
- PHP 7.4+
- MySQL 8.0+
- HTML5, CSS3, JavaScript (ES6+)
- Font Awesome para iconos

---

¡Disfruta de tu clon de YouTube! 🎉