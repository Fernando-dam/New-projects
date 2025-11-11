<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Enviar mensaje
if (isset($_POST['enviar_mensaje'])) {
    $destinatario_id = (int)$_POST['destinatario_id'];
    $asunto = sanitize($_POST['asunto']);
    $mensaje = sanitize($_POST['mensaje']);
    
    $sql = "INSERT INTO mensajes (remitente_id, destinatario_id, asunto, mensaje) 
            VALUES ($usuario_id, $destinatario_id, '$asunto', '$mensaje')";
    
    if ($conn->query($sql)) {
        showMessage('Mensaje enviado exitosamente', 'success');
    } else {
        showMessage('Error al enviar el mensaje', 'danger');
    }
}

// Marcar como leído
if (isset($_GET['leer'])) {
    $mensaje_id = (int)$_GET['leer'];
    $conn->query("UPDATE mensajes SET leido = 1 WHERE id = $mensaje_id AND destinatario_id = $usuario_id");
}

// Obtener usuarios para enviar mensajes
$usuarios = $conn->query("SELECT id, nombre, email FROM usuarios WHERE id != $usuario_id");

// Obtener mensajes recibidos
$mensajes_recibidos = $conn->query("SELECT m.*, u.nombre as remitente_nombre 
                                     FROM mensajes m 
                                     INNER JOIN usuarios u ON m.remitente_id = u.id 
                                     WHERE m.destinatario_id = $usuario_id 
                                     ORDER BY m.fecha_envio DESC");

// Obtener mensajes enviados
$mensajes_enviados = $conn->query("SELECT m.*, u.nombre as destinatario_nombre 
                                    FROM mensajes m 
                                    INNER JOIN usuarios u ON m.destinatario_id = u.id 
                                    WHERE m.remitente_id = $usuario_id 
                                    ORDER BY m.fecha_envio DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>💬 Mensajería</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('recibidos')">Recibidos</button>
                <button class="tab-btn" onclick="showTab('enviados')">Enviados</button>
                <button class="tab-btn" onclick="showTab('nuevo')">Nuevo Mensaje</button>
            </div>
            
            <div id="recibidos" class="tab-content active">
                <div class="card">
                    <h2>Mensajes Recibidos</h2>
                    <?php if ($mensajes_recibidos->num_rows > 0): ?>
                        <div class="messages-list">
                            <?php while ($msg = $mensajes_recibidos->fetch_assoc()): ?>
                                <div class="message-item <?php echo !$msg['leido'] ? 'unread' : ''; ?>">
                                    <div class="message-header">
                                        <strong><?php echo $msg['remitente_nombre']; ?></strong>
                                        <span class="message-date"><?php echo date('d/m/Y H:i', strtotime($msg['fecha_envio'])); ?></span>
                                    </div>
                                    <div class="message-subject">
                                        <strong><?php echo $msg['asunto']; ?></strong>
                                    </div>
                                    <div class="message-preview">
                                        <?php echo substr($msg['mensaje'], 0, 100) . '...'; ?>
                                    </div>
                                    <a href="ver_mensaje.php?id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-primary">Leer</a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-state">No tienes mensajes recibidos.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="enviados" class="tab-content">
                <div class="card">
                    <h2>Mensajes Enviados</h2>
                    <?php if ($mensajes_enviados->num_rows > 0): ?>
                        <div class="messages-list">
                            <?php while ($msg = $mensajes_enviados->fetch_assoc()): ?>
                                <div class="message-item">
                                    <div class="message-header">
                                        <strong>Para: <?php echo $msg['destinatario_nombre']; ?></strong>
                                        <span class="message-date"><?php echo date('d/m/Y H:i', strtotime($msg['fecha_envio'])); ?></span>
                                    </div>
                                    <div class="message-subject">
                                        <strong><?php echo $msg['asunto']; ?></strong>
                                    </div>
                                    <div class="message-preview">
                                        <?php echo substr($msg['mensaje'], 0, 100) . '...'; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p class="empty-state">No has enviado mensajes.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="nuevo" class="tab-content">
                <div class="card">
                    <h2>Enviar Nuevo Mensaje</h2>
                    <form method="POST" class="form-horizontal">
                        <div class="form-group">
                            <label for="destinatario_id">Destinatario</label>
                            <select id="destinatario_id" name="destinatario_id" required>
                                <option value="">Seleccionar destinatario...</option>
                                <?php while ($user = $usuarios->fetch_assoc()): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo $user['nombre'] . ' (' . $user['email'] . ')'; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="asunto">Asunto</label>
                            <input type="text" id="asunto" name="asunto" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mensaje">Mensaje</label>
                            <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" name="enviar_mensaje" class="btn btn-primary">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>