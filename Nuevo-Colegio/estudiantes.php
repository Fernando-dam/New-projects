<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$db = getDBConnection();
$mensaje = '';
$error = '';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear') {
        try {
            $stmt = $db->prepare("INSERT INTO estudiantes (nombre, apellido, fecha_nacimiento, documento, direccion, telefono, email, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['apellido']),
                $_POST['fecha_nacimiento'],
                cleanInput($_POST['documento']),
                cleanInput($_POST['direccion']),
                cleanInput($_POST['telefono']),
                cleanInput($_POST['email']),
                $_POST['fecha_ingreso']
            ]);
            $mensaje = 'Estudiante registrado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al registrar estudiante: ' . $e->getMessage();
        }
    } elseif ($action === 'editar') {
        try {
            $stmt = $db->prepare("UPDATE estudiantes SET nombre=?, apellido=?, fecha_nacimiento=?, documento=?, direccion=?, telefono=?, email=?, fecha_ingreso=? WHERE id=?");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['apellido']),
                $_POST['fecha_nacimiento'],
                cleanInput($_POST['documento']),
                cleanInput($_POST['direccion']),
                cleanInput($_POST['telefono']),
                cleanInput($_POST['email']),
                $_POST['fecha_ingreso'],
                $_POST['id']
            ]);
            $mensaje = 'Estudiante actualizado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al actualizar estudiante';
        }
    } elseif ($action === 'eliminar') {
        try {
            $stmt = $db->prepare("UPDATE estudiantes SET activo = 0 WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $mensaje = 'Estudiante eliminado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al eliminar estudiante';
        }
    }
}

// Obtener lista de estudiantes
$stmt = $db->query("SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellido, nombre");
$estudiantes = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión de Estudiantes</h2>
        <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Estudiante</button>
    </div>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre Completo</th>
                    <th>Fecha Nacimiento</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estudiantes as $est): ?>
                <tr>
                    <td><?php echo htmlspecialchars($est['documento']); ?></td>
                    <td><?php echo htmlspecialchars($est['apellido'] . ', ' . $est['nombre']); ?></td>
                    <td><?php echo formatDate($est['fecha_nacimiento']); ?></td>
                    <td><?php echo htmlspecialchars($est['email']); ?></td>
                    <td><?php echo htmlspecialchars($est['telefono']); ?></td>
                    <td><?php echo formatDate($est['fecha_ingreso']); ?></td>
                    <td class="actions">
                        <button onclick='editarEstudiante(<?php echo json_encode($est); ?>)' 
                                class="btn btn-sm btn-warning">Editar</button>
                        <button onclick="eliminarEstudiante(<?php echo $est['id']; ?>)" 
                                class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para crear/editar estudiante -->
<div id="modalEstudiante" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitulo">Nuevo Estudiante</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" id="action" value="crear">
            <input type="hidden" name="id" id="estudianteId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="apellido">Apellido *</label>
                    <input type="text" id="apellido" name="apellido" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="documento">Documento *</label>
                    <input type="text" id="documento" name="documento" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_nacimiento">Fecha Nacimiento *</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>
            </div>
            
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion">
            </div>
            
            <div class="form-group">
                <label for="fecha_ingreso">Fecha de Ingreso *</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="js/estudiantes.js"></script>

<?php include 'includes/footer.php'; ?>