<header class="header">
    <div class="header-content">
        <div class="header-logo">
            <h1><i class="fas fa-graduation-cap"></i> Colegio Nuevos Horizontes</h1>
        </div>
        
        <div class="header-actions">
            <div class="user-info">
                <span class="user-name"><?php echo $_SESSION['nombre']; ?></span>
                <span class="user-role"><?php echo ucfirst($_SESSION['rol']); ?></span>
            </div>
            <div class="header-buttons">
                <a href="?logout=true" class="btn btn-outline btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>
</header>