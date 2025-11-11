<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

$db = getDBConnection();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear') {
        try {
            $stmt = $db->prepare("INSERT INTO asignaturas (nombre, descripcion, codigo, creditos) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['descripcion']),
                cleanInput($_POST['codigo']),
                $_POST['creditos']
            ]);
            $mensaje = 'Asignatura creada exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al crear asignatura';
        }
    } elseif ($action === 'editar') {
        try {
            $stmt = $db->prepare("UPDATE asignaturas SET nombre=?, descripcion=?, codigo=?, creditos=? WHERE id=?");
            $stmt->execute([
                cleanInput($_POST['nombre']),
                cleanInput($_POST['descripcion']),
                cleanInput($_POST['codigo']),
                $_POST['creditos'],
                $_POST['id']
            ]);
            $mensaje = 'Asignatura actualizada exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al actualizar asignatura';
        }
    } elseif ($action === 'eliminar') {
        try {
            $stmt = $db->prepare("UPDATE asignaturas SET activo = 0 WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $mensaje = 'Asignatura eliminada exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al eliminar asignatura';
        }
    }
}

$stmt = $db->query("SELECT * FROM asignaturas WHERE activo = 1 ORDER BY nombre");
$asignaturas = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión de Asignaturas</h2>
        <button onclick="abrirModal()" class="btn btn-primary">+ Nueva Asignatura</button>
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
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Créditos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($asignaturas as $asig): ?>
                <tr>
                    <td><?php echo htmlspecialchars($asig['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($asig['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($asig['descripcion']); ?></td>
                    <td><?php echo $asig['creditos']; ?></td>
                    <td class="actions">
                        <button onclick='editarAsignatura(<?php echo json_encode($asig); ?>)' 
                                class="btn btn-sm btn-warning">Editar</button>
                        <button onclick="eliminarAsignatura(<?php echo $asig['id']; ?>)" 
                                class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalAsignatura" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitulo">Nueva Asignatura</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" id="action" value="crear">
            <input type="hidden" name="id" id="asignaturaId">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="codigo">Código *</label>
                    <input type="text" id="codigo" name="codigo" required>
                </div>
                
                <div class="form-group">
                    <label for="creditos">Créditos *</label>
                    <input type="number" id="creditos" name="creditos" min="1" max="10" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="nombre">Nombre de Asignatura *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="js/asignaturas.js"></script>

<?php include 'includes/footer.php'; ?>