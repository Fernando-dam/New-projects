<?php
session_start();
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    
    // Crear directorio de uploads si no existe
    $upload_dir = 'uploads/videos/';
    $thumbnail_dir = 'uploads/thumbnails/';
    
    if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if(!is_dir($thumbnail_dir)) mkdir($thumbnail_dir, 0777, true);
    
    // Procesar video
    if(isset($_FILES['video']) && $_FILES['video']['error'] === 0) {
        $video_ext = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
        $video_name = uniqid() . '.' . $video_ext;
        $video_path = $upload_dir . $video_name;
        
        // Procesar thumbnail
        $thumbnail_path = '';
        if(isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $thumb_ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
            $thumb_name = uniqid() . '.' . $thumb_ext;
            $thumbnail_path = $thumbnail_dir . $thumb_name;
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_path);
        }
        
        if(move_uploaded_file($_FILES['video']['tmp_name'], $video_path)) {
            // Obtener duración del video (aproximada)
            $duration = '0:00';
            
            $db = new Database();
            $conn = $db->getConnection();
            
            $query = "INSERT INTO videos (user_id, title, description, video_path, thumbnail, duration, category) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if($stmt->execute([$_SESSION['user_id'], $title, $description, $video_path, $thumbnail_path, $duration, $category])) {
                $success = '¡Video subido exitosamente!';
            } else {
                $error = 'Error al guardar el video en la base de datos';
            }
        } else {
            $error = 'Error al subir el archivo de video';
        }
    } else {
        $error = 'Por favor selecciona un archivo de video';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Video - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="upload-container">
        <div class="upload-box">
            <h1>Subir Video</h1>
            
            <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
                <a href="index.php">Ir al inicio</a>
            </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="upload-form">
                <div class="form-group">
                    <label for="video">Archivo de video *</label>
                    <div class="file-input">
                        <input type="file" id="video" name="video" accept="video/*" required>
                        <label for="video" class="file-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Seleccionar video</span>
                        </label>
                    </div>
                    <small>Formatos: MP4, AVI, MOV, WMV (Max: 500MB)</small>
                </div>
                
                <div class="form-group">
                    <label for="thumbnail">Miniatura</label>
                    <div class="file-input">
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*">
                        <label for="thumbnail" class="file-label">
                            <i class="fas fa-image"></i>
                            <span>Seleccionar imagen</span>
                        </label>
                    </div>
                    <small>Formatos: JPG, PNG (Recomendado: 1280x720)</small>
                </div>
                
                <div class="form-group">
                    <label for="title">Título *</label>
                    <input type="text" id="title" name="title" required maxlength="100" 
                           placeholder="Añade un título que describa tu video">
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea id="description" name="description" rows="5" 
                              placeholder="Cuéntale a los espectadores sobre tu video"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="category">Categoría</label>
                    <select id="category" name="category">
                        <option value="">Selecciona una categoría</option>
                        <option value="music">Música</option>
                        <option value="gaming">Videojuegos</option>
                        <option value="sports">Deportes</option>
                        <option value="entertainment">Entretenimiento</option>
                        <option value="news">Noticias</option>
                        <option value="education">Educación</option>
                        <option value="science">Ciencia y tecnología</option>
                        <option value="travel">Viajes</option>
                        <option value="cooking">Cocina</option>
                        <option value="other">Otros</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="window.location.href='index.php'" class="btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-upload"></i> Subir Video
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Preview de archivos seleccionados
        document.getElementById('video').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if(fileName) {
                this.nextElementSibling.querySelector('span').textContent = fileName;
            }
        });
        
        document.getElementById('thumbnail').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if(fileName) {
                this.nextElementSibling.querySelector('span').textContent = fileName;
            }
        });
    </script>
</body>
</html>