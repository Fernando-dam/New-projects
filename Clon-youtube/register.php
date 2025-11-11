<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Verificar si el usuario ya existe
        $query = "SELECT id FROM users WHERE email = ? OR username = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email, $username]);
        
        if($stmt->fetch()) {
            $error = 'El email o nombre de usuario ya está registrado';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (username, email, password, channel_name) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            
            if($stmt->execute([$username, $email, $hashed_password, $username])) {
                $success = 'Cuenta creada exitosamente. Redirigiendo...';
                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['username'] = $username;
                $_SESSION['avatar'] = 'assets/images/default-avatar.png';
                header('refresh:2;url=index.php');
            } else {
                $error = 'Error al crear la cuenta';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - YouTube Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-logo">
                <i class="fab fa-youtube"></i>
                <h1>YouTube Clone</h1>
            </div>
            
            <h2>Crear Cuenta</h2>
            
            <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <?php if($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username">Nombre de usuario</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmar contraseña</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                
                <button type="submit" class="btn-primary">Registrarse</button>
            </form>
            
            <p class="auth-switch">
                ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
            </p>
        </div>
    </div>
</body>
</html>