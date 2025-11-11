function abrirModal() {
    document.querySelector('#modalMensaje form').reset();
    document.getElementById('modalMensaje').classList.add('active');
    document.getElementById('modalMensaje').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modalMensaje').classList.remove('active');
    document.getElementById('modalMensaje').style.display = 'none';
}