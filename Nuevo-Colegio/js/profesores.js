function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nuevo Profesor';
    document.getElementById('action').value = 'crear';
    document.getElementById('profesorId').value = '';
    document.querySelector('form').reset();
    document.getElementById('fecha_ingreso').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalProfesor').classList.add('active');
    document.getElementById('modalProfesor').style.display = 'flex';
}

function editarProfesor(profesor) {
    document.getElementById('modalTitulo').textContent = 'Editar Profesor';
    document.getElementById('action').value = 'editar';
    document.getElementById('profesorId').value = profesor.id;
    document.getElementById('nombre').value = profesor.nombre;
    document.getElementById('apellido').value = profesor.apellido;
    document.getElementById('documento').value = profesor.documento;
    document.getElementById('especialidad').value = profesor.especialidad || '';
    document.getElementById('telefono').value = profesor.telefono || '';
    document.getElementById('email').value = profesor.email;
    document.getElementById('fecha_ingreso').value = profesor.fecha_ingreso;
    document.getElementById('modalProfesor').classList.add('active');
    document.getElementById('modalProfesor').style.display = 'flex';
}

function eliminarProfesor(id) {
    if (confirm('¿Está seguro de eliminar este profesor?')) {
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

function cerrarModal() {
    document.getElementById('modalProfesor').classList.remove('active');
    document.getElementById('modalProfesor').style.display = 'none';
}