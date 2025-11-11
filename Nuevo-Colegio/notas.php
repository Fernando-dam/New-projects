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
            $stmt = $db->prepare("INSERT INTO notas (inscripcion_id, tipo_evaluacion, nota, fecha_registro, observaciones) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['inscripcion_id'],
                cleanInput($_POST['tipo_evaluacion']),
                $_POST['nota'],
                $_POST['fecha_registro'],
                cleanInput($_POST['observaciones'])
            ]);
            $mensaje = 'Nota registrada exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al registrar nota';
        }
    }
}

// Obtener estudiantes y sus inscripciones
$estudiantes = $db->query("
    SELECT DISTINCT e.id, e.nombre, e.apellido, e.documento
    FROM estudiantes e
    JOIN inscripciones i ON e.id = i.estudiante_id
    WHERE e.activo = 1
    ORDER BY e.apellido, e.nombre
")->fetchAll();

// Si se seleccionó un estudiante, obtener sus notas
$estudiante_id = $_GET['estudiante_id'] ?? null;
$notas = [];

if ($estudiante_id) {
    $stmt = $db->prepare("
        SELECT n.*, i.periodo, a.nombre as asignatura, a.codigo,
               CONCAT(e.nombre, ' ', e.apellido) as estudiante
        FROM notas n
        JOIN inscripciones i ON n.inscripcion_id = i.id
        JOIN asignaturas a ON i.asignatura_id = a.id
        JOIN estudiantes e ON i.estudiante_id = e.id
        WHERE e.id = ?
        ORDER BY i.periodo DESC, a.nombre, n.fecha_registro
    ");
    $stmt->execute([$estudiante_id]);
    $notas = $stmt->fetchAll();
    
    // Obtener inscripciones del estudiante para el formulario
    $inscripciones = $db->prepare("
        SELECT i.*, a.nombre as asignatura, a.codigo
        FROM inscripciones i
        JOIN asignaturas a ON i.asignatura_id = a.id
        WHERE i.estudiante_id = ?
        ORDER BY i.periodo DESC, a.nombre
    ");
    $inscripciones->execute([$estudiante_id]);
    $inscripciones = $inscripciones->fetchAll();
}

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión de Notas</h2>
        <?php if ($estudiante_id): ?>
            <button onclick="abrirModal()" class="btn btn-primary">+ Registrar Nota</button>
        <?php endif; ?>
    </div>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="filter-section">
        <form method="GET" action="" class="filter-form">
            <div class="form-group">
                <label for="estudiante_id">Seleccionar Estudiante:</label>
                <select id="estudiante_id" name="estudiante_id" onchange="this.form.submit()">
                    <option value="">Seleccione un estudiante...</option>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $estudiante_id == $e['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($e['apellido'] . ', ' . $e['nombre'] . ' (' . $e['documento'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>
    
    <?php if ($estudiante_id && count($notas) > 0): ?>
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Periodo</th>
                    <th>Asignatura</th>
                    <th>Tipo Evaluación</th>
                    <th>Nota</th>
                    <th>Fecha</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $periodo_actual = '';
                foreach ($notas as $n): 
                    if ($periodo_actual != $n['periodo']) {
                        $periodo_actual = $n['periodo'];
                        echo "<tr class='periodo-header'><td colspan='6'><strong>Periodo: {$periodo_actual}</strong></td></tr>";
                    }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($n['periodo']); ?></td>
                    <td><?php echo htmlspecialchars($n['asignatura'] . ' (' . $n['codigo'] . ')'); ?></td>
                    <td><?php echo htmlspecialchars($n['tipo_evaluacion']); ?></td>
                    <td><strong><?php echo number_format($n['nota'], 2); ?></strong></td>
                    <td><?php echo formatDate($n['fecha_registro']); ?></td>
                    <td><?php echo htmlspecialchars($n['observaciones']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif ($estudiante_id): ?>
        <div class="alert alert-info">No hay notas registradas para este estudiante.</div>
    <?php else: ?>
        <div class="alert alert-info">Seleccione un estudiante para ver sus notas.</div>
    <?php endif; ?>
</div>

<?php if ($estudiante_id && isset($inscripciones)): ?>
<div id="modalNota" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Registrar Nota</h3>
            <span class="close" onclick="cerrarModal()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear">
            
            <div class="form-group">
                <label for="inscripcion_id">Asignatura *</label>
                <select id="inscripcion_id" name="inscripcion_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($inscripciones as $i): ?>
                        <option value="<?php echo $i['id']; ?>">
                            <?php echo htmlspecialchars($i['asignatura'] . ' - ' . $i['periodo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_evaluacion">Tipo de Evaluación *</label>
                    <select id="tipo_evaluacion" name="tipo_evaluacion" required>
                        <option value="Examen Parcial">Examen Parcial</option>
                        <option value="Examen Final">Examen Final</option>
                        <option value="Trabajo Práctico">Trabajo Práctico</option>
                        <option value="Participación">Participación</option>
                        <option value="Tarea">Tarea</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="nota">Nota (0-10) *</label>
                    <input type="number" id="nota" name="nota" min="0" max="10" step="0.01" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="fecha_registro">Fecha *</label>
                <input type="date" id="fecha_registro" name="fecha_registro" 
                       value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3"></textarea>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="js/notas.js"></script>

<?php include 'includes/footer.php'; ?>