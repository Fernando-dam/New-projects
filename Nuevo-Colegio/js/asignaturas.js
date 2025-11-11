function abrirModal() {
    document.getElementById('modalTitulo').textContent = 'Nueva Asignatura';
    document.getElementById('action').value = 'crear';
    document.getElementById('asignaturaId').value = '';
    document.querySelector('form').reset();
    document.getElementById('modalAsignatura').classList.add('active');
    document.getElementById('modalAsignatura').style.display = 'flex';
}

function editarAsignatura(asignatura) {
    document.getElementById('modalTitulo').textContent = 'Editar Asignatura';
    document.getElementById('action').value = 'editar';
    document.getElementById('asignaturaId').value = asignatura.id;
    document.getElementById('codigo').value = asignatura.codigo;
    document.getElementById('nombre').value = asignatura.nombre;
    document.getElementById('descripcion').value = asignatura.descripcion || '';
    document.getElementById('creditos').value = asignatura.creditos;
    document.getElementById('modalAsignatura').classList.add('active');
    document.getElementById('modalAsignatura').style.display = 'flex';
}

function eliminarAsignatura(id) {
    if (confirm('¿Está seguro de eliminar esta asignatura?')) {
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
    document.getElementById('modalAsignatura').classList.remove('active');
    document.getElementById('modalAsignatura').style.display = 'none';
}