function abrirModal() {
    document.querySelector('#modalNota form').reset();
    document.getElementById('fecha_registro').value = new Date().toISOString().split('T')[0];
    document.getElementById('modalNota').classList.add('active');
    document.getElementById('modalNota').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalNota').classList.remove('active');
    document.getElementById('modalNota').style.display = 'none';
}