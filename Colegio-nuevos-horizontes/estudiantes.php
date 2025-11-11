<?php
session_start();
require_once 'php/auth.php';
require_once 'php/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?error=sesion");
    exit();
}

// Verificar permisos
$auth = new Auth($db);
$auth->checkRole(['admin', 'secretaria']);

// Procesar operaciones CRUD
if ($_POST) {
    require_once 'php/estudiantes.php';
    $estudiantes = new Estudiantes($db);
    
    if (isset($_POST['agregar'])) {
        $estudiantes->crear($_POST);
    } elseif (isset($_POST['editar'])) {
        $estudiantes->actualizar($_POST['id'], $_POST);
    } elseif (isset($_POST['eliminar'])) {
        $estudiantes->eliminar($_POST['id']);
    }
}

// Obtener lista de estudiantes
require_once 'php/estudiantes.php';
$estudiantes = new Estudiantes($db);
$lista_estudiantes = $estudiantes->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Estudiantes - Colegio Nuevos Horizontes</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Gestión de Estudiantes</h1>
                <button class="btn btn-primary" onclick="abrirModal('modalEstudiante')">
                    <i class="fas fa-user-plus"></i> Nuevo Estudiante
                </button>
            </div>
            
            <!-- Lista de Estudiantes -->
            <div class="card">
                <div class="card-header">
                    <h2>Lista de Estudiantes</h2>
                    <div class="card-actions">
                        <input type="text" id="buscarEstudiante" placeholder="Buscar estudiante...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Cédula</th>
                                    <th>Grado</th>
                                    <th>Sección</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lista_estudiantes as $estudiante): ?>
                                <tr>
                                    <td><?php echo $estudiante['id']; ?></td>
                                    <td><?php echo $estudiante['nombre_completo']; ?></td>
                                    <td><?php echo $estudiante['cedula']; ?></td>
                                    <td><?php echo $estudiante['grado']; ?></td>
                                    <td><?php echo $estudiante['seccion']; ?></td>
                                    <td><?php echo $estudiante['telefono']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $estudiante['estado'] == 'activo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($estudiante['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-outline" 
                                                    onclick="editarEstudiante(<?php echo $estudiante['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="eliminarEstudiante(<?php echo $estudiante['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Modal para agregar/editar estudiante -->
    <div id="modalEstudiante" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitulo">Nuevo Estudiante</h3>
                <span class="close" onclick="cerrarModal('modalEstudiante')">&times;</span>
            </div>
            <form id="formEstudiante" method="POST">
                <input type="hidden" id="estudianteId" name="id">
                <div class="modal-body">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nombre_completo">Nombre Completo *</label>
                            <input type="text" id="nombre_completo" name="nombre_completo" required>
                        </div>
                        <div class="form-group">
                            <label for="cedula">Cédula *</label>
                            <input type="text" id="cedula" name="cedula" required>
                        </div>
                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="form-group">
                            <label for="genero">Género</label>
                            <select id="genero" name="genero">
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="grado">Grado *</label>
                            <select id="grado" name="grado" required>
                                <option value="">Seleccionar grado</option>
                                <?php for ($i = 1; $i <= 11; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>° Grado</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="seccion">Sección</label>
                            <input type="text" id="seccion" name="seccion" placeholder="Ej: A, B, C">
                        </div>
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                        <div class="form-group full-width">
                            <label for="direccion">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="nombre_tutor">Nombre del Tutor</label>
                            <input type="text" id="nombre_tutor" name="nombre_tutor">
                        </div>
                        <div class="form-group">
                            <label for="telefono_tutor">Teléfono del Tutor</label>
                            <input type="tel" id="telefono_tutor" name="telefono_tutor">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="cerrarModal('modalEstudiante')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" name="agregar" id="btnSubmit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script src="js/estudiantes.js"></script>
</body>
</html>