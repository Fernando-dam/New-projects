// Funcionalidades del Dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Cargar estadísticas
    cargarEstadisticas();
    
    // Configurar búsqueda en tablas
    const buscarInputs = document.querySelectorAll('input[type="text"][id*="buscar"]');
    buscarInputs.forEach(input => {
        input.addEventListener('input', function() {
            filtrarTabla(this);
        });
    });
});

function cargarEstadisticas() {
    // Simular carga de datos estadísticos
    setTimeout(() => {
        document.getElementById('total-estudiantes').textContent = '125';
        document.getElementById('total-profesores').textContent = '18';
        document.getElementById('total-asignaturas').textContent = '24';
        document.getElementById('total-facturas').textContent = '89';
    }, 1000);
}

function filtrarTabla(input) {
    const filter = input.value.toLowerCase();
    const table = input.closest('.card').querySelector('table');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Funciones para modales
function abrirModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let modal of modals) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}