<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Obtener estadísticas
$usuario_id = $_SESSION['usuario_id'];

$cursos_inscritos = $conn->query("SELECT COUNT(*) as total FROM registros WHERE usuario_id = $usuario_id AND estado = 'inscrito'")->fetch_assoc()['total'];
$solicitudes_pendientes = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE usuario_id = $usuario_id AND estado = 'pendiente'")->fetch_assoc()['total'];
$citas_pendientes = $conn->query("SELECT COUNT(*) as total FROM citas WHERE usuario_id = $usuario_id AND estado = 'pendiente'")->fetch_assoc()['total'];
$mensajes_nuevos = $conn->query("SELECT COUNT(*) as total FROM mensajes WHERE destinatario_id = $usuario_id AND leido = 0")->fetch_assoc()['total'];

// Estadísticas para admin
if (isAdmin()) {
    $total_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios WHERE tipo = 'usuario'")->fetch_assoc()['total'];
    $total_cursos = $conn->query("SELECT COUNT(*) as total FROM cursos WHERE estado = 'activo'")->fetch_assoc()['total'];
    $solicitudes_admin = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE estado = 'pendiente'")->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="dashboard">
                <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?>!</h1>
                
                <?php displayMessage(); ?>
                
                <?php if (isAdmin()): ?>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">👥</div>
                            <div class="stat-info">
                                <h3><?php echo $total_usuarios; ?></h3>
                                <p>Usuarios Registrados</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📚</div>
                            <div class="stat-info">
                                <h3><?php echo $total_cursos; ?></h3>
                                <p>Cursos Activos</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📋</div>
                            <div class="stat-info">
                                <h3><?php echo $solicitudes_admin; ?></h3>
                                <p>Solicitudes Pendientes</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📅</div>
                            <div class="stat-info">
                                <h3><?php echo $citas_pendientes; ?></h3>
                                <p>Citas Pendientes</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📚</div>
                            <div class="stat-info">
                                <h3><?php echo $cursos_inscritos; ?></h3>
                                <p>Cursos Inscritos</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📜</div>
                            <div class="stat-info">
                                <h3><?php echo $solicitudes_pendientes; ?></h3>
                                <p>Solicitudes Pendientes</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📅</div>
                            <div class="stat-info">
                                <h3><?php echo $citas_pendientes; ?></h3>
                                <p>Citas Pendientes</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">💬</div>
                            <div class="stat-info">
                                <h3><?php echo $mensajes_nuevos; ?></h3>
                                <p>Mensajes Nuevos</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="quick-actions">
                    <h2>Acciones Rápidas</h2>
                    <div class="action-buttons">
                        <?php if (!isAdmin()): ?>
                            <a href="cursos.php" class="action-btn">
                                <span class="icon">📚</span>
                                <span>Ver Cursos</span>
                            </a>
                            <a href="solicitudes.php" class="action-btn">
                                <span class="icon">📜</span>
                                <span>Solicitar Documentos</span>
                            </a>
                            <a href="citas.php" class="action-btn">
                                <span class="icon">📅</span>
                                <span>Agendar Cita</span>
                            </a>
                            <a href="mensajes.php" class="action-btn">
                                <span class="icon">💬</span>
                                <span>Mensajes</span>
                            </a>
                        <?php else: ?>
                            <a href="admin_cursos.php" class="action-btn">
                                <span class="icon">📚</span>
                                <span>Gestionar Cursos</span>
                            </a>
                            <a href="admin_solicitudes.php" class="action-btn">
                                <span class="icon">📋</span>
                                <span>Ver Solicitudes</span>
                            </a>
                            <a href="admin_usuarios.php" class="action-btn">
                                <span class="icon">👥</span>
                                <span>Gestionar Usuarios</span>
                            </a>
                            <a href="mensajes.php" class="action-btn">
                                <span class="icon">💬</span>
                                <span>Mensajes</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>