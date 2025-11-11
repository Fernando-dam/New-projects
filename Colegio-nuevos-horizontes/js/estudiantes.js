// Funcionalidades específicas para estudiantes
function editarEstudiante(id) {
    // Simular obtención de datos del estudiante
    const estudiante = {
        id: id,
        nombre_completo: 'Ana María Rodríguez',
        cedula: '001-1234567-8',
        fecha_nacimiento: '2010-05-15',
        genero: 'F',
        grado: '7',
        seccion: 'A',
        telefono: '809-123-4567',
        email: 'ana.rodriguez@email.com',
        direccion: 'Calle Principal #123',
        nombre_tutor: 'José Rodríguez',
        telefono_tutor: '809-987-6543'
    };
    
    // Llenar el formulario
    document.getElementById('estudianteId').value = estudiante.id;
    document.getElementById('nombre_completo').value = estudiante.nombre_completo;
    document.getElementById('cedula').value = estudiante.cedula;
    document.getElementById('fecha_nacimiento').value = estudiante.fecha_nacimiento;
    document.getElementById('genero').value = estudiante.genero;
    document.getElementById('grado').value = estudiante.grado;
    document.getElementById('seccion').value = estudiante.seccion;
    document.getElementById('telefono').value = estudiante.telefono;
    document.getElementById('email').value = estudiante.email;
    document.getElementById('direccion').value = estudiante.direccion;
    document.getElementById('nombre_tutor').value = estudiante.nombre_tutor;
    document.getElementById('telefono_tutor').value = estudiante.telefono_tutor;
    
    // Cambiar el formulario para edición
    document.getElementById('modalTitulo').textContent = 'Editar Estudiante';
    document.getElementById('btnSubmit').name = 'editar';
    document.getElementById('btnSubmit').textContent = 'Actualizar';
    
    abrirModal('modalEstudiante');
}

function eliminarEstudiante(id) {
    if (confirm('¿Está seguro de que desea eliminar este estudiante?')) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'eliminar';
        deleteInput.value = '1';
        
        form.appendChild(idInput);
        form.appendChild(deleteInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Limpiar formulario al abrir modal para nuevo estudiante
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalEstudiante');
    modal.addEventListener('show', function() {
        if (!document.getElementById('estudianteId').value) {
            document.getElementById('formEstudiante').reset();
            document.getElementById('modalTitulo').textContent = 'Nuevo Estudiante';
            document.getElementById('btnSubmit').name = 'agregar';
            document.getElementById('btnSubmit').textContent = 'Guardar';
        }
    });
});