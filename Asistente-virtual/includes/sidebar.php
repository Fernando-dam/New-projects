<aside class="sidebar">
    <div class="sidebar-menu">
        <a href="dashboard.php" class="sidebar-link">
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </a>
        
        <?php if (!isAdmin()): ?>
            <a href="cursos.php" class="sidebar-link">
                <span class="icon">📚</span>
                <span>Cursos</span>
            </a>
            <a href="solicitudes.php" class="sidebar-link">
                <span class="icon">📜</span>
                <span>Solicitudes</span>
            </a>
            <a href="citas.php" class="sidebar-link">
                <span class="icon">📅</span>
                <span>Citas</span>
            </a>
            <a href="verificacion.php" class="sidebar-link">
                <span class="icon">✅</span>
                <span>Verificación</span>
            </a>
        <?php else: ?>
            <a href="admin_cursos.php" class="sidebar-link">
                <span class="icon">📚</span>
                <span>Gestionar Cursos</span>
            </a>
            <a href="admin_solicitudes.php" class="sidebar-link">
                <span class="icon">📋</span>
                <span>Solicitudes</span>
            </a>
            <a href="admin_usuarios.php" class="sidebar-link">
                <span class="icon">👥</span>
                <span>Usuarios</span>
            </a>
            <a href="admin_citas.php" class="sidebar-link">
                <span class="icon">📅</span>
                <span>Citas</span>
            </a>
        <?php endif; ?>
        
        <a href="mensajes.php" class="sidebar-link">
            <span class="icon">💬</span>
            <span>Mensajes</span>
        </a>
        
        <a href="perfil.php" class="sidebar-link">
            <span class="icon">👤</span>
            <span>Mi Perfil</span>
        </a>
    </div>
</aside>