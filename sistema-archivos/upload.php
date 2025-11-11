<?php
require_once 'config_files.php';

$mensaje = '';
$tipo_mensaje = '';

// Procesar subida de archivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['archivo'])) {
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    
    // Verificar si se subió un archivo
    if ($_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES['archivo'];
        
        // Validar tamaño
        if ($archivo['size'] > MAX_FILE_SIZE) {
            $mensaje = "El archivo es demasiado grande. Tamaño máximo: " . formatearTamano(MAX_FILE_SIZE);
            $tipo_mensaje = "error";
        }
        // Validar tipo de archivo
        elseif (!in_array($archivo['type'], ALLOWED_TYPES)) {
            $mensaje = "Tipo de archivo no permitido. Solo se permiten imágenes, documentos PDF, Word, Excel, txt y archivos comprimidos.";
            $tipo_mensaje = "error";
        }
        else {
            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_guardado = uniqid() . '_' . time() . '.' . $extension;
            $ruta_destino = UPLOAD_DIR . $nombre_guardado;
            
            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                // Guardar en base de datos
                $sql = "INSERT INTO archivos (nombre_original, nombre_guardado, ruta, tipo_archivo, tamano, descripcion) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);
                $stmt->bind_param("ssssis", 
                    $archivo['name'], 
                    $nombre_guardado, 
                    $ruta_destino, 
                    $archivo['type'], 
                    $archivo['size'], 
                    $descripcion
                );
                
                if ($stmt->execute()) {
                    $mensaje = "Archivo subido exitosamente: " . htmlspecialchars($archivo['name']);
                    $tipo_mensaje = "success";
                } else {
                    $mensaje = "Error al guardar en la base de datos: " . $conexion->error;
                    $tipo_mensaje = "error";
                }
                $stmt->close();
            } else {
                $mensaje = "Error al subir el archivo al servidor.";
                $tipo_mensaje = "error";
            }
        }
    } else {
        $mensaje = "Error al subir el archivo. Código: " . $_FILES['archivo']['error'];
        $tipo_mensaje = "error";
    }
}

// Eliminar archivo
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    
    // Obtener información del archivo
    $sql = "SELECT * FROM archivos WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($archivo = $resultado->fetch_assoc()) {
        // Eliminar archivo físico
        if (file_exists($archivo['ruta'])) {
            unlink($archivo['ruta']);
        }
        
        // Eliminar de la base de datos
        $sql = "DELETE FROM archivos WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $mensaje = "Archivo eliminado exitosamente";
            $tipo_mensaje = "success";
        }
    }
    $stmt->close();
}

// Obtener todos los archivos
$sql = "SELECT * FROM archivos ORDER BY fecha_subida DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Carga de Archivos</title>
    <link rel="stylesheet" href="styles_upload.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>📤 Sistema de Carga de Archivos</h1>
            <p>Sube y administra tus archivos de forma segura</p>
        </header>
        
        <div class="content">
            <?php if ($mensaje): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>" id="mensaje">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <!-- Formulario de subida -->
            <div class="upload-section">
                <h2>📁 Subir Nuevo Archivo</h2>
                <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                    <div class="file-input-wrapper">
                        <input type="file" id="archivo" name="archivo" required accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        <label for="archivo" class="file-label">
                            <span class="file-icon">📎</span>
                            <span id="fileName">Seleccionar archivo</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción del archivo:</label>
                        <textarea id="descripcion" name="descripcion" rows="3" placeholder="Agrega una descripción opcional..."></textarea>
                    </div>
                    
                    <div class="file-info">
                        <p><strong>Tamaño máximo:</strong> <?php echo formatearTamano(MAX_FILE_SIZE); ?></p>
                        <p><strong>Tipos permitidos:</strong> Imágenes (JPG, PNG, GIF, WEBP), PDF, Word, Excel, TXT, ZIP, RAR</p>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <span>⬆️ Subir Archivo</span>
                    </button>
                </form>
            </div>
            
            <!-- Lista de archivos -->
            <div class="files-section">
                <h2>📋 Archivos Subidos</h2>
                
                <?php if ($resultado->num_rows > 0): ?>
                    <div class="files-grid">
                        <?php while ($archivo = $resultado->fetch_assoc()): ?>
                            <div class="file-card">
                                <div class="file-icon-display">
                                    <?php
                                    $icono = '📄';
                                    if (strpos($archivo['tipo_archivo'], 'image') !== false) {
                                        $icono = '🖼️';
                                    } elseif (strpos($archivo['tipo_archivo'], 'pdf') !== false) {
                                        $icono = '📕';
                                    } elseif (strpos($archivo['tipo_archivo'], 'word') !== false || strpos($archivo['tipo_archivo'], 'document') !== false) {
                                        $icono = '📘';
                                    } elseif (strpos($archivo['tipo_archivo'], 'excel') !== false || strpos($archivo['tipo_archivo'], 'sheet') !== false) {
                                        $icono = '📗';
                                    } elseif (strpos($archivo['tipo_archivo'], 'zip') !== false || strpos($archivo['tipo_archivo'], 'rar') !== false) {
                                        $icono = '🗜️';
                                    }
                                    echo $icono;
                                    ?>
                                </div>
                                <div class="file-info-card">
                                    <h3><?php echo htmlspecialchars($archivo['nombre_original']); ?></h3>
                                    <?php if ($archivo['descripcion']): ?>
                                        <p class="file-description"><?php echo htmlspecialchars($archivo['descripcion']); ?></p>
                                    <?php endif; ?>
                                    <div class="file-meta">
                                        <span>📊 <?php echo formatearTamano($archivo['tamano']); ?></span>
                                        <span>📅 <?php echo date('d/m/Y H:i', strtotime($archivo['fecha_subida'])); ?></span>
                                    </div>
                                </div>
                                <div class="file-actions">
                                    <a href="<?php echo $archivo['ruta']; ?>" download class="btn-icon btn-download" title="Descargar">
                                        ⬇️
                                    </a>
                                    <a href="<?php echo $archivo['ruta']; ?>" target="_blank" class="btn-icon btn-view" title="Ver">
                                        👁️
                                    </a>
                                    <button onclick="confirmarEliminar(<?php echo $archivo['id']; ?>, '<?php echo htmlspecialchars($archivo['nombre_original']); ?>')" class="btn-icon btn-delete" title="Eliminar">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>📂 No hay archivos subidos</p>
                        <p>Sube tu primer archivo usando el formulario anterior</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="script_upload.js"></script>
</body>
</html>
<?php
$conexion->close();
?>
