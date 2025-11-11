<?php
session_start();
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Nuevos Horizontes - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-graduation-cap"></i> Colegio Nuevos Horizontes</h1>
            <p>Sistema de Administración Escolar</p>
        </div>
        
        <div class="login-form">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    $errors = [
                        'credenciales' => 'Usuario o contraseña incorrectos',
                        'vacio' => 'Por favor complete todos los campos',
                        'sesion' => 'Debe iniciar sesión para acceder'
                    ];
                    echo $errors[$_GET['error']] ?? 'Error desconocido';
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="php/auth.php" method="POST">
                <div class="form-group">
                    <label for="usuario"><i class="fas fa-user"></i> Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="rol"><i class="fas fa-user-tag"></i> Rol</label>
                    <select id="rol" name="rol" required>
                        <option value="">Seleccione su rol</option>
                        <option value="admin">Administrador</option>
                        <option value="docente">Docente</option>
                        <option value="secretaria">Secretaría</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Ingresar al Sistema</button>
            </form>
            
            <div class="login-info">
                <h3>Credenciales de Prueba:</h3>
                <p><strong>Admin:</strong> admin / admin123</p>
                <p><strong>Docente:</strong> profesor1 / prof123</p>
                <p><strong>Secretaría:</strong> secretaria / secre123</p>
            </div>
        </div>
    </div>
</body>
</html>