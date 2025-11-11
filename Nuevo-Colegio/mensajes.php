<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

if (!hasRole(['administrador', 'secretaria'])) {
    redirect('dashboard.php');
}

$db = getDBConnection();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'enviar') {
        try {
            $stmt = $db->prepare("INSERT INTO mensajes (remitente_id, destinatario_id, asunto, mensaje) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['destinatario_id'],
                cleanInput($_POST['asunto']),
                cleanInput($_POST['mensaje'])
            ]);
            $mensaje = 'Mensaje enviado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al enviar mensaje';
        }
    } elseif ($action === 'marcar_leido') {
        try {
            $stmt = $db->prepare("UPDATE mensajes SET leido = 1 WHERE id = ? AND destinatario_id = ?");
            $stmt->execute([$_POST['id'], $_SESSION['user_id']]);
        } catch (PDOException $e) {
            $error = 'Error al marcar mensaje';
        }
    }
}

// Obtener mensajes recibidos
$stmt = $db->prepare("
    SELECT m.*, u.nombre as remitente_nombre
    FROM mensajes m
    JOIN usuarios u ON m.remitente_id = u.id
    WHERE m.destinatario_id = ?
    ORDER BY m.fecha_envio DESC
");
$stmt->execute([$_SESSION['user_id']]);
$mensajesRecibidos = $stmt->fetchAll();

// Obtener mensajes enviados
$stmt = $db->prepare("
    SELECT m.*, u.nombre as destinatario_nombre
    FROM mensajes m
    JOIN usuarios u ON m.destinatario_id = u.id
    WHERE m.remitente_id = ?
    ORDER BY m.fecha_envio DESC
");
$stmt->execute([$_SESSION['user_id']]);
$mensajesEnviados = $stmt->fetchAll();

// Obtener usuarios para el formulario
$stmt = $db->prepare("SELECT * FROM usuarios WHERE id != ? AND activo = 1 AND rol IN ('administrador', 'secretaria') ORDER BY nombre");
$stmt->execute([$_SESSION['user_id']]);
$usuarios = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Mensajería Interna</h2>
        <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Mensaje</button>
    </div>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="tabs">
        <button class="tab-btn active" onclick="cambiarTab('recibidos')">Recibidos</button>
        <button class="tab-btn" onclick="cambiarTab('enviados')">Enviados</button>
    </div>
    
    <div id="tab-recibidos" class="tab-content active">
        <h3>Mensajes Recibidos</h3>
        <div class="mensajes-lista">
            <?php if (count($mensajesRecibidos) > 0): ?>
                <?php foreach ($mensajesRecibidos as $m): ?>
                <div class="mensaje-item <?php echo $m['leido'] ? '' : 'no-leido'; ?>">
                    <div class="mensaje-header">
                        <strong>De: <?php echo htmlspecialchars($m['remitente_nombre']); ?></strong>
                        <span class="mensaje-fecha"><?php echo date('d/m/Y H:i', strtotime($m['fecha_envio'])); ?></span>
                    </div>
                    <div class="mensaje-asunto">
                        <strong><?php echo htmlspecialchars($m['asunto']); ?></strong>
                    </div>
                    <div class="mensaje-contenido">
                        <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>
                    </div>
                    <?php if (!$m['leido']): ?>
                    <form method="POST" action="" style="display:inline;">
                        <input type="hidden" name="action" value="marcar_leido">
                        <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                        <button type="submit" class="btn btn-sm btn-secondary">Marcar como leído</button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No tienes mensajes recibidos.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <div id="tab-enviados" class="tab-content">
        <h3>Mensajes Enviados</h3>
        <div class="mensajes-lista">
            <?php if (count($mensajesEnviados) > 0): ?>
                <?php foreach ($mensajesEnviados as $m): ?>
                <div class="mensaje-item">
                    <div class="mensaje-header">
                        <strong>Para: <?php echo htmlspecialchars($m['destinatario_nombre']); ?></strong>
                        <span class="mensaje-fecha"><?php echo date('d/m/Y H:i', strtotime($m['fecha_envio'])); ?></span>
                    </div>
                    <div class="mensaje-asunto">
                        <strong><?php echo htmlspecialchars($m['asunto']); ?></strong>
                    </div>
                    <div class="mensaje-contenido">
                        <?php echo nl2br(htmlspecialchars($m['mensaje'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No has enviado mensajes.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="modalMensaje" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nuevo Mensaje</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="enviar">
            
            <div class="form-group">
                <label for="destinatario_id">Para *</label>
                <select id="destinatario_id" name="destinatario_id" required>
                    <option value="">Seleccione destinatario...</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?php echo $u['id']; ?>">
                            <?php echo htmlspecialchars($u['nombre'] . ' (' . ucfirst($u['rol']) . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="asunto">Asunto *</label>
                <input type="text" id="asunto" name="asunto" required>
            </div>
            
            <div class="form-group">
                <label for="mensaje">Mensaje *</label>
                <textarea id="mensaje" name="mensaje" rows="6" required></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
            </div>
        </form>
    </div>
</div>

<script src="js/mensajes.js"></script>

<?php include 'includes/footer.php'; ?>