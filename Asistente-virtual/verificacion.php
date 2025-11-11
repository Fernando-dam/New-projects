<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario
$sql = "SELECT * FROM usuarios WHERE id = " . $usuario_id;
$result = $conn->query($sql);
$usuario = $result->fetch_assoc();

// Generar nuevo código
if (isset($_POST['generar_codigo'])) {
    $codigo = rand(100000, 999999);
    $update_sql = "UPDATE usuarios SET codigo_verificacion = '" . $codigo . "' WHERE id = " . $usuario_id;
    $conn->query($update_sql);
    showMessage('Nuevo código generado: ' . $codigo, 'success');
    header("Location: verificacion.php");
    exit();
}

// Verificar código
if (isset($_POST['verificar_codigo'])) {
    $codigo_ingresado = $_POST['codigo'];
    
    if ($codigo_ingresado == $usuario['codigo_verificacion']) {
        $conn->query("UPDATE usuarios SET verificado = 1 WHERE id = " . $usuario_id);
        $_SESSION['verificado'] = 1;
        showMessage('Cuenta verificada exitosamente', 'success');
        header("Location: verificacion.php");
        exit();
    } else {
        showMessage('Código incorrecto', 'danger');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Cuenta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="main-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="content">
            <div class="page-header">
                <h1>✅ Verificación de Cuenta</h1>
            </div>
            
            <?php displayMessage(); ?>
            
            <div class="card" style="max-width: 600px; margin: 0 auto;">
                <?php if ($usuario['verificado'] == 1): ?>
                    <div style="text-align: center; padding: 40px;">
                        <div style="font-size: 5rem; color: #28a745; margin-bottom: 20px;">✓</div>
                        <h2>Cuenta Verificada</h2>
                        <p>Tu cuenta ha sido verificada exitosamente.</p>
                        <span class="badge badge-aprobada" style="font-size: 1.1rem; padding: 10px 20px;">✓ Verificado</span>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 20px 20px;">
                        <div style="font-size: 5rem; color: #ffc107; margin-bottom: 20px;">⚠</div>
                        <h2>Cuenta No Verificada</h2>
                        <p>Necesitas verificar tu cuenta para acceder a todas las funcionalidades.</p>
                        <span class="badge badge-pendiente" style="font-size: 1.1rem; padding: 10px 20px;">⚠ No Verificado</span>
                    </div>
                    
                    <hr style="margin: 30px 0;">
                    
                    <h3>Paso 1: Generar Código</h3>
                    <p>Haz clic en el botón para generar tu código de verificación.</p>
                    <form method="POST">
                        <button type="submit" name="generar_codigo" class="btn btn-primary">🔑 Generar Código</button>
                    </form>
                    
                    <?php if (!empty($usuario['codigo_verificacion'])): ?>
                        <hr style="margin: 30px 0;">
                        
                        <h3>Paso 2: Tu Código</h3>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0; border: 2px dashed #4A90E2;">
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">Código de Verificación</p>
                            <div style="font-size: 2.5rem; font-weight: bold; color: #4A90E2; letter-spacing: 10px; margin: 10px 0; font-family: 'Courier New', monospace;">
                                <?php echo $usuario['codigo_verificacion']; ?>
                            </div>
                        </div>
                        
                        <hr style="margin: 30px 0;">
                        
                        <h3>Paso 3: Verificar</h3>
                        <p>Ingresa el código que generaste:</p>
                        <form method="POST">
                            <div class="form-group">
                                <label for="codigo">Código de Verificación</label>
                                <input type="text" 
                                       id="codigo" 
                                       name="codigo" 
                                       maxlength="6" 
                                       pattern="[0-9]{6}" 
                                       placeholder="000000"
                                       style="text-align: center; font-size: 1.5rem; letter-spacing: 8px; padding: 15px; font-family: 'Courier New', monospace;"
                                       required>
                            </div>
                            <button type="submit" name="verificar_codigo" class="btn btn-success btn-block">✓ Verificar Cuenta</button>
                        </form>
                    <?php endif; ?>
                    
                    <hr style="margin: 30px 0;">
                    
                    <div style="background: #e7f3ff; border-left: 4px solid #4A90E2; padding: 15px; border-radius: 5px;">
                        <h3 style="margin-top: 0; color: #4A90E2;">📌 Importante</h3>
                        <ul style="margin: 10px 0; padding-left: 20px;">
                            <li>El código es único para tu cuenta</li>
                            <li>Puedes generar un nuevo código en cualquier momento</li>
                            <li>Una vez verificada, tendrás acceso completo</li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        const codigoInput = document.getElementById('codigo');
        if (codigoInput) {
            codigoInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    </script>
</body>
</html>