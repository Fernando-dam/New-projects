function abrirModalGasto() {
    document.querySelector('#modalGasto form').reset();
    document.getElementById('fecha_gasto').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalGasto').classList.add('active');
    document.getElementById('modalGasto').style.display = 'flex';
}

function cerrarModalGasto() {
    document.getElementById('modalGasto').classList.remove('active');
    document.getElementById('modalGasto').style.display = 'none';
}

function abrirModalIngreso() {
    document.querySelector('#modalIngreso form').reset();
    document.getElementById('fecha_ingreso').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalIngreso').classList.add('active');
    document.getElementById('modalIngreso').style.display = 'flex';
}

function cerrarModalIngreso() {
    document.getElementById('modalIngreso').classList.remove('active');
    document.getElementById('modalIngreso').style.display = 'none';
}

function abrirModalFactura() {
    document.querySelector('#modalFactura form').reset();
    document.getElementById('fecha_emision').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalFactura').classList.add('active');
    document.getElementById('modalFactura').style.display = 'flex';
}

function cerrarModalFactura() {
    document.getElementById('modalFactura').classList.remove('active');
    document.getElementById('modalFactura').style.display = 'none';
}