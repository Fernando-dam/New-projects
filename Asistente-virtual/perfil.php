<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Actualizar perfil
if (isset($_POST['actualizar_perfil'])) {
    $nombre = sanitize($_POST['nombre']);
    $email = sanitize($_POST['email']);
    
    // Verificar si el email ya existe (excepto el actual)
    $check = $conn->query("SELECT id FROM usuarios WHERE email = '$email' AND id != $usuario_id");
    
    if ($check->num_rows > 0) {
        showMessage('El correo electrónico ya está en uso', 'danger');
    } else {
        $sql = "UPDATE usuarios SET nombre = '$nombre', email = '$email' WHERE id = $usuario_id";
        
        if ($conn->query($sql)) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['email'] = $email;
            showMessage('Perfil actualizado exitosamente', 'success');
        } else {
            showMessage('Error al actualizar el perfil', 'danger');
        }
    }
}

// Cambiar contraseña
if (isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Verificar contraseña actual
    $user = $conn->query("SELECT password FROM usuarios WHERE id = $usuario_id")->fetch_assoc();
    
    if (!password_verify($password_actual, $user['password'])) {
        showMessage('La contraseña actual es incorrecta', 'danger');
    } else if ($password_nueva !== $password_confirmar) {
        showMessage('Las contraseñas nuevas no coinciden', 'danger');
    } else if (strlen($password_nueva) < 6) {
        showMessage('La contraseña debe tener al menos 6 caracteres', 'danger');
    } else {
        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $conn->query("UPDATE usuarios SET password = '$password_hash' WHERE id = $usuario_id");
        showMessage('Contraseña cambiada exitosamente', 'success');
    }
}

// Obtener datos del usuario
$usuario = $conn->query("SELECT * FROM usuarios WHERE id = $usuario_id")->fetch_assoc();

// Obtener estadísticas del usuario
$total_cursos = $conn->query("SELECT COUNT(*) as total FROM registros WHERE usuario_id = $usuario_id")->fetch_assoc()['total'];
$cursos_completados = $conn->query("SELECT COUNT(*) as total FROM registros WHERE usuario_id = $usuario_id AND estado = 'completado'")->fetch_assoc()['total'];
$certificados = $conn->query("SELECT COUNT(*) as total FROM solicitudes WHERE usuario_id = $usuario_id AND estado = 'aprobada'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        
        .profile-sidebar {
            background: white;
            padding: 0;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .profile-card {
            padding: 30px;
            text-align: center;
        }
        
        .profile-avatar {
            margin-bottom: 20px;
        }
        
        .avatar-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            margin: 0 auto;
        }
        
        .profile-email {
            color: #666;
            margin: 10px 0;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 2px solid #f0f0f0;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }
        
        .profile-info {
            text-align: left;
            margin-top: 20px;
        }
        
        .profile-info p {
            margin: 10px 0;
        }
        
        .profile-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>👤 Mi Perfil</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="profile-container">
                <div class="profile-sidebar">
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <div class="avatar-circle">
                                <?php echo strtoupper(substr($usuario['nombre'], 0, 2)); ?>
                            </div>
                        </div>
                        <h2><?php echo $usuario['nombre']; ?></h2>
                        <p class="profile-email"><?php echo $usuario['email']; ?></p>
                        <span class="badge badge-<?php echo $usuario['tipo']; ?>">
                            <?php echo ucfirst($usuario['tipo']); ?>
                        </span>
                        
                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $total_cursos; ?></div>
                                <div class="stat-label">Cursos</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $cursos_completados; ?></div>
                                <div class="stat-label">Completados</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $certificados; ?></div>
                                <div class="stat-label">Certificados</div>
                            </div>
                        </div>
                        
                        <div class="profile-info">
                            <p><strong>Fecha de registro:</strong></p>
                            <p><?php echo date('d/m/Y', strtotime($usuario['fecha_registro'])); ?></p>
                            
                            <p><strong>Estado:</strong></p>
                            <p>
                                <?php if ($usuario['verificado']): ?>
                                    <span class="badge badge-aprobada">✓ Verificado</span>
                                <?php else: ?>
                                    <span class="badge badge-pendiente">⚠ No verificado</span>
                                    <br><br>
                                    <a href="verificacion.php" class="btn btn-sm btn-primary">Verificar ahora</a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="profile-content">
                    <div class="card">
                        <h2>Editar Información Personal</h2>
                        <form method="POST" class="form-horizontal">
                            <div class="form-group">
                                <label for="nombre">Nombre Completo</label>
                                <input type="text" id="nombre" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Correo Electrónico</label>
                                <input type="email" id="email" name="email" value="<?php echo $usuario['email']; ?>" required>
                            </div>
                            
                            <button type="submit" name="actualizar_perfil" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                    
                    <div class="card">
                        <h2>Cambiar Contraseña</h2>
                        <form method="POST" class="form-horizontal">
                            <div class="form-group">
                                <label for="password_actual">Contraseña Actual</label>
                                <input type="password" id="password_actual" name="password_actual" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password_nueva">Nueva Contraseña</label>
                                <input type="password" id="password_nueva" name="password_nueva" required minlength="6">
                            </div>
                            
                            <div class="form-group">
                                <label for="password_confirmar">Confirmar Nueva Contraseña</label>
                                <input type="password" id="password_confirmar" name="password_confirmar" required minlength="6">
                            </div>
                            
                            <button type="submit" name="cambiar_password" class="btn btn-primary">Cambiar Contraseña</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>