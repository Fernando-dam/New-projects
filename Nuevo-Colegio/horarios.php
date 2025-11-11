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
            $stmt = $db->prepare("INSERT INTO horarios (asignatura_id, profesor_id, dia_semana, hora_inicio, hora_fin, aula) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['asignatura_id'],
                $_POST['profesor_id'],
                $_POST['dia_semana'],
                $_POST['hora_inicio'],
                $_POST['hora_fin'],
                cleanInput($_POST['aula'])
            ]);
            $mensaje = 'Horario creado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al crear horario';
        }
    } elseif ($action === 'eliminar') {
        try {
            $stmt = $db->prepare("DELETE FROM horarios WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $mensaje = 'Horario eliminado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al eliminar horario';
        }
    }
}

// Obtener datos para el formulario
$profesores = $db->query("SELECT * FROM profesores WHERE activo = 1 ORDER BY apellido, nombre")->fetchAll();
$asignaturas = $db->query("SELECT * FROM asignaturas WHERE activo = 1 ORDER BY nombre")->fetchAll();

// Obtener horarios
$stmt = $db->query("
    SELECT h.*, a.nombre as asignatura, a.codigo, 
           CONCAT(p.nombre, ' ', p.apellido) as profesor
    FROM horarios h
    JOIN asignaturas a ON h.asignatura_id = a.id
    JOIN profesores p ON h.profesor_id = p.id
    ORDER BY 
        FIELD(h.dia_semana, 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'),
        h.hora_inicio
");
$horarios = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión de Horarios</h2>
        <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Horario</button>
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
                    <th>Día</th>
                    <th>Hora</th>
                    <th>Asignatura</th>
                    <th>Profesor</th>
                    <th>Aula</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($horarios as $h): ?>
                <tr>
                    <td><?php echo ucfirst($h['dia_semana']); ?></td>
                    <td><?php echo substr($h['hora_inicio'], 0, 5) . ' - ' . substr($h['hora_fin'], 0, 5); ?></td>
                    <td><?php echo htmlspecialchars($h['asignatura'] . ' (' . $h['codigo'] . ')'); ?></td>
                    <td><?php echo htmlspecialchars($h['profesor']); ?></td>
                    <td><?php echo htmlspecialchars($h['aula']); ?></td>
                    <td class="actions">
                        <button onclick="eliminarHorario(<?php echo $h['id']; ?>)" 
                                class="btn btn-sm btn-danger">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalHorario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nuevo Horario</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear">
            
            <div class="form-group">
                <label for="asignatura_id">Asignatura *</label>
                <select id="asignatura_id" name="asignatura_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($asignaturas as $a): ?>
                        <option value="<?php echo $a['id']; ?>">
                            <?php echo htmlspecialchars($a['nombre'] . ' (' . $a['codigo'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="profesor_id">Profesor *</label>
                <select id="profesor_id" name="profesor_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($profesores as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['apellido'] . ', ' . $p['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="dia_semana">Día *</label>
                    <select id="dia_semana" name="dia_semana" required>
                        <option value="lunes">Lunes</option>
                        <option value="martes">Martes</option>
                        <option value="miercoles">Miércoles</option>
                        <option value="jueves">Jueves</option>
                        <option value="viernes">Viernes</option>
                        <option value="sabado">Sábado</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="aula">Aula *</label>
                    <input type="text" id="aula" name="aula" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="hora_inicio">Hora Inicio *</label>
                    <input type="time" id="hora_inicio" name="hora_inicio" required>
                </div>
                
                <div class="form-group">
                    <label for="hora_fin">Hora Fin *</label>
                    <input type="time" id="hora_fin" name="hora_fin" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script src="js/horarios.js"></script>

<?php include 'includes/footer.php'; ?>