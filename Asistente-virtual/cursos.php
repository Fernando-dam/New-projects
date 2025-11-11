<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Inscribirse en un curso
if (isset($_POST['inscribir'])) {
    $curso_id = (int)$_POST['curso_id'];
    
    // Verificar si ya está inscrito
    $check = $conn->query("SELECT id FROM registros WHERE usuario_id = $usuario_id AND curso_id = $curso_id");
    
    if ($check->num_rows > 0) {
        showMessage('Ya estás inscrito en este curso', 'warning');
    } else {
        // Verificar cupos disponibles
        $curso = $conn->query("SELECT cupos_disponibles FROM cursos WHERE id = $curso_id")->fetch_assoc();
        
        if ($curso['cupos_disponibles'] > 0) {
            $conn->query("INSERT INTO registros (usuario_id, curso_id) VALUES ($usuario_id, $curso_id)");
            $conn->query("UPDATE cursos SET cupos_disponibles = cupos_disponibles - 1 WHERE id = $curso_id");
            showMessage('¡Inscripción exitosa!', 'success');
        } else {
            showMessage('Lo sentimos, no hay cupos disponibles', 'danger');
        }
    }
}

// Obtener todos los cursos activos
$cursos_sql = "SELECT * FROM cursos WHERE estado = 'activo' ORDER BY fecha_inicio ASC";
$cursos = $conn->query($cursos_sql);

// Obtener cursos inscritos del usuario
$mis_cursos_sql = "SELECT c.*, r.fecha_inscripcion, r.estado as estado_registro 
                   FROM cursos c 
                   INNER JOIN registros r ON c.id = r.curso_id 
                   WHERE r.usuario_id = $usuario_id 
                   ORDER BY r.fecha_inscripcion DESC";
$mis_cursos = $conn->query($mis_cursos_sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>📚 Cursos Disponibles</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="showTab('disponibles')">Cursos Disponibles</button>
                <button class="tab-btn" onclick="showTab('inscritos')">Mis Cursos</button>
            </div>
            
            <div id="disponibles" class="tab-content active">
                <div class="courses-grid">
                    <?php while ($curso = $cursos->fetch_assoc()): ?>
                        <?php
                        $inscrito = $conn->query("SELECT id FROM registros WHERE usuario_id = $usuario_id AND curso_id = {$curso['id']}")->num_rows > 0;
                        ?>
                        <div class="course-card">
                            <div class="course-header">
                                <h3><?php echo $curso['nombre']; ?></h3>
                                <span class="course-price">$<?php echo number_format($curso['precio'], 2); ?></span>
                            </div>
                            <div class="course-body">
                                <p><?php echo $curso['descripcion']; ?></p>
                                <div class="course-info">
                                    <span><strong>Duración:</strong> <?php echo $curso['duracion']; ?></span>
                                    <span><strong>Inicio:</strong> <?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?></span>
                                    <span><strong>Cupos:</strong> <?php echo $curso['cupos_disponibles']; ?></span>
                                </div>
                            </div>
                            <div class="course-footer">
                                <?php if ($inscrito): ?>
                                    <button class="btn btn-success" disabled>✓ Inscrito</button>
                                <?php elseif ($curso['cupos_disponibles'] > 0): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="curso_id" value="<?php echo $curso['id']; ?>">
                                        <button type="submit" name="inscribir" class="btn btn-primary">Inscribirse</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Sin Cupos</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div id="inscritos" class="tab-content">
                <?php if ($mis_cursos->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Fecha Inscripción</th>
                                    <th>Inicio</th>
                                    <th>Duración</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($curso = $mis_cursos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $curso['nombre']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($curso['fecha_inscripcion'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($curso['fecha_inicio'])); ?></td>
                                        <td><?php echo $curso['duracion']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $curso['estado_registro']; ?>">
                                                <?php echo ucfirst($curso['estado_registro']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="solicitudes.php?curso_id=<?php echo $curso['id']; ?>" class="btn btn-sm btn-secondary">
                                                Solicitar Certificado
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No estás inscrito en ningún curso todavía.</p>
                        <button class="btn btn-primary" onclick="showTab('disponibles')">Ver Cursos Disponibles</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="js/script.js"></script>
</body>
</html>