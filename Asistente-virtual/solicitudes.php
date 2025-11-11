<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Crear nueva solicitud
if (isset($_POST['crear_solicitud'])) {
    $tipo = sanitize($_POST['tipo']);
    $curso_id = (int)$_POST['curso_id'];
    
    // Verificar que el usuario está inscrito en el curso
    $check = $conn->query("SELECT id FROM registros WHERE usuario_id = $usuario_id AND curso_id = $curso_id");
    
    if ($check->num_rows > 0) {
        $sql = "INSERT INTO solicitudes (usuario_id, tipo, curso_id) VALUES ($usuario_id, '$tipo', $curso_id)";
        if ($conn->query($sql)) {
            showMessage('Solicitud enviada exitosamente', 'success');
        } else {
            showMessage('Error al crear la solicitud', 'danger');
        }
    } else {
        showMessage('Debes estar inscrito en el curso para solicitar documentos', 'warning');
    }
}

// Obtener cursos del usuario
$cursos_usuario = $conn->query("SELECT c.* FROM cursos c 
                                 INNER JOIN registros r ON c.id = r.curso_id 
                                 WHERE r.usuario_id = $usuario_id");

// Obtener solicitudes del usuario
$solicitudes = $conn->query("SELECT s.*, c.nombre as curso_nombre 
                             FROM solicitudes s 
                             INNER JOIN cursos c ON s.curso_id = c.id 
                             WHERE s.usuario_id = $usuario_id 
                             ORDER BY s.fecha_solicitud DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>📜 Solicitud de Documentos</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="card">
                <h2>Nueva Solicitud</h2>
                <form method="POST" class="form-horizontal">
                    <div class="form-group">
                        <label for="tipo">Tipo de Documento</label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccionar...</option>
                            <option value="certificado">Certificado</option>
                            <option value="constancia">Constancia</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="curso_id">Curso</label>
                        <select id="curso_id" name="curso_id" required>
                            <option value="">Seleccionar curso...</option>
                            <?php while ($curso = $cursos_usuario->fetch_assoc()): ?>
                                <option value="<?php echo $curso['id']; ?>">
                                    <?php echo $curso['nombre']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="crear_solicitud" class="btn btn-primary">Enviar Solicitud</button>
                </form>
            </div>
            
            <div class="card">
                <h2>Mis Solicitudes</h2>
                <?php if ($solicitudes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Curso</th>
                                    <th>Fecha Solicitud</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sol = $solicitudes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo ucfirst($sol['tipo']); ?></td>
                                        <td><?php echo $sol['curso_nombre']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($sol['fecha_solicitud'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $sol['estado']; ?>">
                                                <?php echo ucfirst($sol['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($sol['estado'] === 'aprobada'): ?>
                                                <a href="descargar_documento.php?id=<?php echo $sol['id']; ?>" class="btn btn-sm btn-success">
                                                    Descargar
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">En proceso</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="empty-state">No tienes solicitudes registradas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>