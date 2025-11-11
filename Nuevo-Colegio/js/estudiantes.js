// Abrir modal para crear estudiante
function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Estudiante';
    document.getElementById('action').value = 'crear';
    document.getElementById('estudianteId').value = '';
    document.querySelector('form').reset();
    document.getElementById('fecha_ingreso').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalEstudiante').classList.add('active');
    document.getElementById('modalEstudiante').style.display = 'flex';
}

// Abrir modal para editar estudiante
function editarEstudiante(estudiante) {
    document.getElementById('modalTitulo').textContent = 'Editar Estudiante';
    document.getElementById('action').value = 'editar';
    document.getElementById('estudianteId').value = estudiante.id;
    document.getElementById('nombre').value = estudiante.nombre;
    document.getElementById('apellido').value = estudiante.apellido;
    document.getElementById('fecha_nacimiento').value = estudiante.fecha_nacimiento;
    document.getElementById('documento').value = estudiante.documento;
    document.getElementById('direccion').value = estudiante.direccion || '';
    document.getElementById('telefono').value = estudiante.telefono || '';
    document.getElementById('email').value = estudiante.email || '';
    document.getElementById('fecha_ingreso').value = estudiante.fecha_ingreso;
    document.getElementById('modalEstudiante').classList.add('active');
    document.getElementById('modalEstudiante').style.display = 'flex';
}

// Eliminar estudiante
function eliminarEstudiante(id) {
    if (confirm('¿Está seguro de eliminar este estudiante?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'eliminar';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Cerrar modal
function cerrarModal() {
    document.getElementById('modalEstudiante').classList.remove('active');
    document.getElementById('modalEstudiante').style.display = 'none';
}