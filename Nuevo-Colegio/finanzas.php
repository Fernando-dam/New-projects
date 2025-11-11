<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('index.php');
}

if (!hasRole(['administrador', 'secretaria'])) {
    redirect('dashboard.php');
}

$db = getDBConnection();
$mensaje = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'crear_gasto') {
        try {
            $stmt = $db->prepare("INSERT INTO gastos (descripcion, monto, fecha, categoria, usuario_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                cleanInput($_POST['descripcion']),
                $_POST['monto'],
                $_POST['fecha'],
                cleanInput($_POST['categoria']),
                $_SESSION['user_id']
            ]);
            $mensaje = 'Gasto registrado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al registrar gasto';
        }
    } elseif ($action === 'crear_ingreso') {
        try {
            $stmt = $db->prepare("INSERT INTO ingresos (descripcion, monto, fecha, categoria, estudiante_id, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                cleanInput($_POST['descripcion']),
                $_POST['monto'],
                $_POST['fecha'],
                cleanInput($_POST['categoria']),
                $_POST['estudiante_id'] ?: null,
                $_SESSION['user_id']
            ]);
            $mensaje = 'Ingreso registrado exitosamente';
        } catch (PDOException $e) {
            $error = 'Error al registrar ingreso';
        }
    } elseif ($action === 'crear_factura') {
        try {
            $numero_factura = 'FAC-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $stmt = $db->prepare("INSERT INTO facturas (numero_factura, estudiante_id, concepto, monto, fecha_emision, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $numero_factura,
                $_POST['estudiante_id'],
                cleanInput($_POST['concepto']),
                $_POST['monto'],
                $_POST['fecha_emision'],
                $_SESSION['user_id']
            ]);
            $mensaje = 'Factura creada exitosamente: ' . $numero_factura;
        } catch (PDOException $e) {
            $error = 'Error al crear factura';
        }
    }
}

// Obtener resumen financiero del mes actual
$mesActual = date('Y-m');
$stmt = $db->prepare("SELECT SUM(monto) as total FROM gastos WHERE DATE_FORMAT(fecha, '%Y-%m') = ?");
$stmt->execute([$mesActual]);
$totalGastos = $stmt->fetch()['total'] ?? 0;

$stmt = $db->prepare("SELECT SUM(monto) as total FROM ingresos WHERE DATE_FORMAT(fecha, '%Y-%m') = ?");
$stmt->execute([$mesActual]);
$totalIngresos = $stmt->fetch()['total'] ?? 0;

// Obtener transacciones recientes
$gastos = $db->query("SELECT * FROM gastos ORDER BY fecha DESC LIMIT 10")->fetchAll();
$ingresos = $db->query("SELECT i.*, CONCAT(e.nombre, ' ', e.apellido) as estudiante FROM ingresos i LEFT JOIN estudiantes e ON i.estudiante_id = e.id ORDER BY i.fecha DESC LIMIT 10")->fetchAll();
$facturas = $db->query("SELECT f.*, CONCAT(e.nombre, ' ', e.apellido) as estudiante FROM facturas f JOIN estudiantes e ON f.estudiante_id = e.id ORDER BY f.fecha_emision DESC LIMIT 10")->fetchAll();

// Obtener estudiantes para formularios
$estudiantes = $db->query("SELECT * FROM estudiantes WHERE activo = 1 ORDER BY apellido, nombre")->fetchAll();

include 'includes/header.php';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Gestión Financiera</h2>
    </div>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo $mensaje; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="stats-grid">
        <div class="stat-card green">
            <h4>Ingresos del Mes</h4>
            <h2><?php echo formatCurrency($totalIngresos); ?></h2>
        </div>
        
        <div class="stat-card red">
            <h4>Gastos del Mes</h4>
            <h2><?php echo formatCurrency($totalGastos); ?></h2>
        </div>
        
        <div class="stat-card blue">
            <h4>Balance del Mes</h4>
            <h2><?php echo formatCurrency($totalIngresos - $totalGastos); ?></h2>
        </div>
    </div>
    
    <div class="action-buttons">
        <button onclick="abrirModalGasto()" class="btn btn-danger">+ Registrar Gasto</button>
        <button onclick="abrirModalIngreso()" class="btn btn-success">+ Registrar Ingreso</button>
        <button onclick="abrirModalFactura()" class="btn btn-primary">+ Crear Factura</button>
    </div>
    
    <div class="tabs">
        <button class="tab-btn active" onclick="cambiarTab('gastos')">Gastos</button>
        <button class="tab-btn" onclick="cambiarTab('ingresos')">Ingresos</button>
        <button class="tab-btn" onclick="cambiarTab('facturas')">Facturas</button>
    </div>
    
    <div id="tab-gastos" class="tab-content active">
        <h3>Gastos Recientes</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gastos as $g): ?>
                <tr>
                    <td><?php echo formatDate($g['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($g['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($g['categoria']); ?></td>
                    <td><strong><?php echo formatCurrency($g['monto']); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="tab-ingresos" class="tab-content">
        <h3>Ingresos Recientes</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Estudiante</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ingresos as $i): ?>
                <tr>
                    <td><?php echo formatDate($i['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($i['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($i['categoria']); ?></td>
                    <td><?php echo htmlspecialchars($i['estudiante'] ?? 'N/A'); ?></td>
                    <td><strong><?php echo formatCurrency($i['monto']); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <div id="tab-facturas" class="tab-content">
        <h3>Facturas Recientes</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Estudiante</th>
                    <th>Concepto</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f['numero_factura']); ?></td>
                    <td><?php echo formatDate($f['fecha_emision']); ?></td>
                    <td><?php echo htmlspecialchars($f['estudiante']); ?></td>
                    <td><?php echo htmlspecialchars($f['concepto']); ?></td>
                    <td><strong><?php echo formatCurrency($f['monto']); ?></strong></td>
                    <td><span class="badge badge-<?php echo $f['estado']; ?>"><?php echo ucfirst($f['estado']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modales -->
<div id="modalGasto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Registrar Gasto</h3>
            <span class="close" onclick="cerrarModalGasto()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear_gasto">
            
            <div class="form-group">
                <label for="descripcion_gasto">Descripción *</label>
                <input type="text" id="descripcion_gasto" name="descripcion" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="monto_gasto">Monto *</label>
                    <input type="number" id="monto_gasto" name="monto" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_gasto">Fecha *</label>
                    <input type="date" id="fecha_gasto" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="categoria_gasto">Categoría</label>
                <select id="categoria_gasto" name="categoria">
                    <option value="Servicios">Servicios</option>
                    <option value="Suministros">Suministros</option>
                    <option value="Salarios">Salarios</option>
                    <option value="Mantenimiento">Mantenimiento</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModalGasto()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalIngreso" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Registrar Ingreso</h3>
            <span class="close" onclick="cerrarModalIngreso()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear_ingreso">
            
            <div class="form-group">
                <label for="descripcion_ingreso">Descripción *</label>
                <input type="text" id="descripcion_ingreso" name="descripcion" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="monto_ingreso">Monto *</label>
                    <input type="number" id="monto_ingreso" name="monto" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_ingreso">Fecha *</label>
                    <input type="date" id="fecha_ingreso" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="categoria_ingreso">Categoría</label>
                <select id="categoria_ingreso" name="categoria">
                    <option value="Matrícula">Matrícula</option>
                    <option value="Mensualidad">Mensualidad</option>
                    <option value="Donación">Donación</option>
                    <option value="Otros">Otros</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="estudiante_id_ingreso">Estudiante (opcional)</label>
                <select id="estudiante_id_ingreso" name="estudiante_id">
                    <option value="">N/A</option>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?php echo $e['id']; ?>">
                            <?php echo htmlspecialchars($e['apellido'] . ', ' . $e['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModalIngreso()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalFactura" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Crear Factura</h3>
            <span class="close" onclick="cerrarModalFactura()">&times;</span>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear_factura">
            
            <div class="form-group">
                <label for="estudiante_id_factura">Estudiante *</label>
                <select id="estudiante_id_factura" name="estudiante_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($estudiantes as $e): ?>
                        <option value="<?php echo $e['id']; ?>">
                            <?php echo htmlspecialchars($e['apellido'] . ', ' . $e['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="concepto">Concepto *</label>
                <input type="text" id="concepto" name="concepto" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="monto_factura">Monto *</label>
                    <input type="number" id="monto_factura" name="monto" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_emision">Fecha Emisión *</label>
                    <input type="date" id="fecha_emision" name="fecha_emision" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" onclick="cerrarModalFactura()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear Factura</button>
            </div>
        </form>
    </div>
</div>

<script src="js/finanzas.js"></script>

<?php include 'includes/footer.php'; ?>