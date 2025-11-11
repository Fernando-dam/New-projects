<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Crear nueva cita
if (isset($_POST['crear_cita'])) {
    $fecha = sanitize($_POST['fecha']);
    $hora = sanitize($_POST['hora']);
    $motivo = sanitize($_POST['motivo']);
    
    $sql = "INSERT INTO citas (usuario_id, fecha, hora, motivo) VALUES ($usuario_id, '$fecha', '$hora', '$motivo')";
    if ($conn->query($sql)) {
        showMessage('Cita agendada exitosamente', 'success');
    } else {
        showMessage('Error al agendar la cita', 'danger');
    }
}

// Cancelar cita
if (isset($_GET['cancelar'])) {
    $cita_id = (int)$_GET['cancelar'];
    $conn->query("UPDATE citas SET estado = 'cancelada' WHERE id = $cita_id AND usuario_id = $usuario_id");
    showMessage('Cita cancelada', 'info');
    redirect('citas.php');
}

// Obtener citas del usuario
$citas = $conn->query("SELECT * FROM citas WHERE usuario_id = $usuario_id ORDER BY fecha DESC, hora DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>📅 Gestión de Citas</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="card">
                <h2>Agendar Nueva Cita</h2>
                <form method="POST" class="form-horizontal">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" id="fecha" name="fecha" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="hora">Hora</label>
                            <input type="time" id="hora" name="hora" required min="08:00" max="18:00">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo">Motivo de la Cita</label>
                        <textarea id="motivo" name="motivo" rows="4" required placeholder="Describe brevemente el motivo de tu cita..."></textarea>
                    </div>
                    
                    <button type="submit" name="crear_cita" class="btn btn-primary">Agendar Cita</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Mis Citas</h2>
                <?php if ($citas->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($cita = $citas->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($cita['fecha'])); ?></td>
                                        <td><?php echo date('H:i', strtotime($cita['hora'])); ?></td>
                                        <td><?php echo substr($cita['motivo'], 0, 50) . '...'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $cita['estado']; ?>">
                                                <?php echo ucfirst($cita['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($cita['estado'] === 'pendiente'): ?>
                                                <a href="?cancelar=<?php echo $cita['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('¿Seguro que deseas cancelar esta cita?')">
                                                    Cancelar
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="empty-state">No tienes citas agendadas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>