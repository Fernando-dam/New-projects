<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Sistema de Administración</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>🎓 <?php echo SITE_NAME; ?></h1>
            </div>
            
            <button class="nav-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    🏠 Dashboard
                </a></li>
                
                <li><a href="estudiantes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'estudiantes.php' ? 'active' : ''; ?>">
                    👨‍🎓 Estudiantes
                </a></li>
                
                <li><a href="profesores.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profesores.php' ? 'active' : ''; ?>">
                    👨‍🏫 Profesores
                </a></li>
                
                <li><a href="asignaturas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'asignaturas.php' ? 'active' : ''; ?>">
                    📚 Asignaturas
                </a></li>
                
                <li><a href="horarios.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'horarios.php' ? 'active' : ''; ?>">
                    🕐 Horarios
                </a></li>
                
                <li><a href="notas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'notas.php' ? 'active' : ''; ?>">
                    📝 Notas
                </a></li>
                
                <?php if (hasRole(['administrador', 'secretaria'])): ?>
                <li><a href="finanzas.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'finanzas.php' ? 'active' : ''; ?>">
                    💵 Finanzas
                </a></li>
                
                <li><a href="mensajes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'mensajes.php' ? 'active' : ''; ?>">
                    ✉️ Mensajes
                </a></li>
                <?php endif; ?>
                
                <li class="user-menu">
                    <span class="user-name">
                        👤 <?php echo $_SESSION['user_nombre']; ?>
                        <small>(<?php echo ucfirst($_SESSION['user_rol']); ?>)</small>
                    </span>
                    <a href="logout.php" class="logout-btn">🚪 Salir</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <main class="main-content">