<aside class="sidebar">
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'secretaria'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'estudiantes.php' ? 'active' : ''; ?>">
                <a href="estudiantes.php">
                    <i class="fas fa-users"></i>
                    <span>Estudiantes</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] == 'admin'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'profesores.php' ? 'active' : ''; ?>">
                <a href="profesores.php">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Profesores</span>
                </a>
            </li>
            <?php endif; ?>
            
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'horarios.php' ? 'active' : ''; ?>">
                <a href="horarios.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Horarios</span>
                </a>
            </li>
            
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'docente'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'notas.php' ? 'active' : ''; ?>">
                <a href="notas.php">
                    <i class="fas fa-edit"></i>
                    <span>Calificaciones</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'secretaria'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'facturas.php' ? 'active' : ''; ?>">
                <a href="facturas.php">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Facturación</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'secretaria'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'finanzas.php' ? 'active' : ''; ?>">
                <a href="finanzas.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Finanzas</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] == 'admin' || $_SESSION['rol'] == 'secretaria'): ?>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'mensajes.php' ? 'active' : ''; ?>">
                <a href="mensajes.php">
                    <i class="fas fa-envelope"></i>
                    <span>Mensajería</span>
                    <span class="badge">3</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($_SESSION['rol'] == 'admin'): ?>
            <li class="nav-item">
                <a href="configuracion.php">
                    <i class="fas fa-cog"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>