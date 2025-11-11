<?php
session_start();
require_once 'php/auth.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?error=sesion");
    exit();
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Colegio Nuevos Horizontes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard Principal</h1>
                <p>Bienvenido, <?php echo $usuario; ?> (<?php echo ucfirst($rol); ?>)</p>
            </div>
            
            <!-- Estadísticas Rápidas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Estudiantes</h3>
                        <span class="stat-number" id="total-estudiantes">0</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Profesores</h3>
                        <span class="stat-number" id="total-profesores">0</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Asignaturas</h3>
                        <span class="stat-number" id="total-asignaturas">0</span>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Facturas</h3>
                        <span class="stat-number" id="total-facturas">0</span>
                    </div>
                </div>
            </div>
            
            <!-- Acciones Rápidas -->
            <div class="quick-actions">
                <h2>Acciones Rápidas</h2>
                <div class="actions-grid">
                    <?php if ($rol == 'admin' || $rol == 'secretaria'): ?>
                    <a href="estudiantes.php" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <span>Registrar Estudiante</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($rol == 'admin'): ?>
                    <a href="profesores.php" class="action-card">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Registrar Profesor</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($rol == 'docente' || $rol == 'admin'): ?>
                    <a href="notas.php" class="action-card">
                        <i class="fas fa-edit"></i>
                        <span>Registrar Notas</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="horarios.php" class="action-card">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Ver Horarios</span>
                    </a>
                    
                    <?php if ($rol == 'admin' || $rol == 'secretaria'): ?>
                    <a href="facturas.php" class="action-card">
                        <i class="fas fa-file-invoice"></i>
                        <span>Generar Factura</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($rol == 'admin' || $rol == 'secretaria'): ?>
                    <a href="mensajes.php" class="action-card">
                        <i class="fas fa-envelope"></i>
                        <span>Mensajería</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Últimas Actividades -->
            <div class="recent-activities">
                <h2>Actividades Recientes</h2>
                <div class="activities-list">
                    <div class="activity-item">
                        <i class="fas fa-user-plus activity-icon new-user"></i>
                        <div class="activity-content">
                            <p>Nuevo estudiante registrado: María González</p>
                            <span class="activity-time">Hace 2 horas</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <i class="fas fa-edit activity-icon grades"></i>
                        <div class="activity-content">
                            <p>Notas actualizadas en Matemáticas - 10mo Grado</p>
                            <span class="activity-time">Hace 5 horas</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <i class="fas fa-file-invoice activity-icon invoice"></i>
                        <div class="activity-content">
                            <p>Factura generada para la familia Rodríguez</p>
                            <span class="activity-time">Ayer</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/dashboard.js"></script>
</body>
</html>