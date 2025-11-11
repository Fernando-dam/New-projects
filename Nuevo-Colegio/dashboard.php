<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$db = getDBConnection();

// Obtener estadísticas
$stmt = $db->query("SELECT COUNT(*) as total FROM estudiantes WHERE activo = 1");
$totalEstudiantes = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM profesores WHERE activo = 1");
$totalProfesores = $stmt->fetch()['total'];

$stmt = $db->query("SELECT COUNT(*) as total FROM asignaturas WHERE activo = 1");
$totalAsignaturas = $stmt->fetch()['total'];

// Gastos e ingresos del mes actual
$mesActual = date('Y-m');
$stmt = $db->prepare("SELECT SUM(monto) as total FROM gastos WHERE DATE_FORMAT(fecha, '%Y-%m') = ?");
$stmt->execute([$mesActual]);
$gastosMes = $stmt->fetch()['total'] ?? 0;

$stmt = $db->prepare("SELECT SUM(monto) as total FROM ingresos WHERE DATE_FORMAT(fecha, '%Y-%m') = ?");
$stmt->execute([$mesActual]);
$ingresosMes = $stmt->fetch()['total'] ?? 0;

include 'includes/header.php';
?>

<div class="dashboard">
    <div class="page-header">
        <h2>Panel de Control</h2>
        <p>Bienvenido/a, <strong><?php echo $_SESSION['user_nombre']; ?></strong></p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">👨‍🎓</div>
            <div class="stat-details">
                <h3><?php echo $totalEstudiantes; ?></h3>
                <p>Estudiantes</p>
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-icon">👨‍🏫</div>
            <div class="stat-details">
                <h3><?php echo $totalProfesores; ?></h3>
                <p>Profesores</p>
            </div>
        </div>
        
        <div class="stat-card purple">
            <div class="stat-icon">📚</div>
            <div class="stat-details">
                <h3><?php echo $totalAsignaturas; ?></h3>
                <p>Asignaturas</p>
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon">💰</div>
            <div class="stat-details">
                <h3><?php echo formatCurrency($ingresosMes - $gastosMes); ?></h3>
                <p>Balance Mensual</p>
            </div>
        </div>
    </div>
    
    <div class="dashboard-grid">
        <div class="dashboard-section">
            <h3>Acceso Rápido</h3>
            <div class="quick-actions">
                <a href="estudiantes.php" class="quick-action-btn">
                    <span class="icon">👨‍🎓</span>
                    <span>Estudiantes</span>
                </a>
                <a href="profesores.php" class="quick-action-btn">
                    <span class="icon">👨‍🏫</span>
                    <span>Profesores</span>
                </a>
                <a href="asignaturas.php" class="quick-action-btn">
                    <span class="icon">📚</span>
                    <span>Asignaturas</span>
                </a>
                <a href="horarios.php" class="quick-action-btn">
                    <span class="icon">🕐</span>
                    <span>Horarios</span>
                </a>
                <a href="notas.php" class="quick-action-btn">
                    <span class="icon">📝</span>
                    <span>Notas</span>
                </a>
                <a href="finanzas.php" class="quick-action-btn">
                    <span class="icon">💵</span>
                    <span>Finanzas</span>
                </a>
            </div>
        </div>
        
        <div class="dashboard-section">
            <h3>Resumen Financiero</h3>
            <div class="finance-summary">
                <div class="finance-item income">
                    <span>Ingresos del mes:</span>
                    <strong><?php echo formatCurrency($ingresosMes); ?></strong>
                </div>
                <div class="finance-item expense">
                    <span>Gastos del mes:</span>
                    <strong><?php echo formatCurrency($gastosMes); ?></strong>
                </div>
                <hr>
                <div class="finance-item balance">
                    <span>Balance:</span>
                    <strong><?php echo formatCurrency($ingresosMes - $gastosMes); ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>