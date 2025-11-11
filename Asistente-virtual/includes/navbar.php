<nav class="navbar">
    <div class="navbar-brand">
        <a href="dashboard.php">🎓 Universidad X</a>
    </div>
    <div class="navbar-menu">
        <span class="user-info">👤 <?php echo $_SESSION['nombre']; ?></span>
        <a href="perfil.php" class="navbar-link">Mi Perfil</a>
        <a href="logout.php" class="navbar-link">Cerrar Sesión</a>
    </div>
</nav>