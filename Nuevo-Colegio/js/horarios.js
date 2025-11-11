function abrirModal() {
    document.querySelector('form').reset();
    document.getElementById('modalHorario').classList.add('active');
    document.getElementById('modalHorario').style.display = 'flex';
}

function eliminarHorario(id) {
    if (confirm('¿Está seguro de eliminar este horario?')) {
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
    document.getElementById('modalHorario').classList.remove('active');
    document.getElementById('modalHorario').style.display = 'none';
}