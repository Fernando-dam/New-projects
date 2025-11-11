<?php
require_once 'config.php';

// Si ya está logueado, redirigir al dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🎓 Asistente Virtual</h1>
            <p class="subtitle">Universidad X - Sistema de Inscripción de Cursos</p>
        </header>

        <div class="welcome-section">
            <?php displayMessage(); ?>
            
            <div class="features">
                <div class="feature-card">
                    <div class="icon">📚</div>
                    <h3>Registro de Cursos</h3>
                    <p>Inscríbete en cursos de formación de manera rápida y sencilla</p>
                </div>
                
                <div class="feature-card">
                    <div class="icon">📜</div>
                    <h3>Solicitud de Documentos</h3>
                    <p>Solicita certificados y constancias de tus cursos completados</p>
                </div>
                
                <div class="feature-card">
                    <div class="icon">📅</div>
                    <h3>Gestión de Citas</h3>
                    <p>Agenda citas con nuestro personal administrativo</p>
                </div>
                
                <div class="feature-card">
                    <div class="icon">💬</div>
                    <h3>Mensajería</h3>
                    <p>Comunícate directamente con estudiantes y administradores</p>
                </div>
            </div>

            <div class="auth-buttons">
                <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
                <a href="register.php" class="btn btn-secondary">Registrarse</a>
            </div>
        </div>

        <footer class="footer">
            <p>&copy; 2025 Universidad X. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>