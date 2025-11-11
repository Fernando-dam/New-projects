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
            $stmt = $db->prepare("INSERT INTO profesores (nombre, apellido, documento, especialidad, telefono, email, fecha_ingreso) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['apellido']),
                cleanInput($_POST['documento']),
                cleanInput($_POST['especialidad']),
                cleanInput($_POST['telefono']),
                cleanInput($_POST['email']),
                $_POST['fecha_ingreso']
            ]);
            $mensaje = 'Profesor registrado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al registrar profesor';
        }
    } elseif ($action === 'editar') {
        try {
            $stmt = $db->prepare("UPDATE profesores SET nombre=?, apellido=?, documento=?, especialidad=?, telefono=?, email=?, fecha_ingreso=? WHERE id=?");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['apellido']),
                cleanInput($_POST['documento']),
                cleanInput($_POST['especialidad']),
                cleanInput($_POST['telefono']),
                cleanInput($_POST['email']),
                $_POST['fecha_ingreso'],
                $_POST['id']
            ]);
            $mensaje = 'Profesor actualizado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al actualizar profesor';
        }
    } elseif ($action === 'eliminar') {
        try {
            $stmt = $db->prepare("UPDATE profesores SET activo = 0 WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $mensaje = 'Profesor eliminado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al eliminar profesor';
        }
    }
}

// Obtener lista de profesores
$stmt = $db->query("SELECT * FROM profesores WHERE activo = 1 ORDER BY apellido, nombre");
$profesores = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión de Profesores</h2>
        <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Profesor</button>
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
                    <th>Especialidad</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profesores as $prof): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prof['documento']); ?></td>
                    <td><?php echo htmlspecialchars($prof['apellido'] . ', ' . $prof['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($prof['especialidad']); ?></td>
                    <td><?php echo htmlspecialchars($prof['email']); ?></td>
                    <td><?php echo htmlspecialchars($prof['telefono']); ?></td>
                    <td><?php echo formatDate($prof['fecha_ingreso']); ?></td>
                    <td class="actions">
                        <button onclick='editarProfesor(<?php echo json_encode($prof); ?>)' 
                                class="btn btn-sm btn-warning">Editar</button>
                        <button onclick="eliminarProfesor(<?php echo $prof['id']; ?>)" 
                                class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modalProfesor" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitulo">Nuevo Profesor</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" id="action" value="crear">
            <input type="hidden" name="id" id="profesorId">
            
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
                    <label for="especialidad">Especialidad</label>
                    <input type="text" id="especialidad" name="especialidad">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono">
                </div>
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

<script src="js/profesores.js"></script>

<?php include 'includes/footer.php'; ?>