// Cargar archivos al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarArchivos();
    
    // Manejar el envío del formulario
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        subirArchivo();
    });
});

// Función para subir archivo
function subirArchivo() {
    const formData = new FormData(document.getElementById('uploadForm'));
    const mensajeDiv = document.getElementById('mensaje');
    
    // Validar que se haya seleccionado un archivo
    const archivoInput = document.getElementById('archivo');
    if (!archivoInput.files.length) {
        mostrarMensaje('Por favor, selecciona un archivo', 'warning');
        return;
    }
    
    // Validar tamaño del archivo (5MB máximo)
    const archivo = archivoInput.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (archivo.size > maxSize) {
        mostrarMensaje('El archivo es demasiado grande. Máximo 5MB', 'danger');
        return;
    }
    
    // Mostrar spinner de carga
    mensajeDiv.innerHTML = '<div class="alert alert-info">Subiendo archivo...</div>';
    
    // Enviar archivo
    fetch('php/subir.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            document.getElementById('uploadForm').reset();
            cargarArchivos();
        } else {
            mostrarMensaje(data.message, 'danger');
        }
    })
    .catch(error => {
        mostrarMensaje('Error al subir el archivo: ' + error, 'danger');
    });
}

// Función para cargar y mostrar archivos
function cargarArchivos() {
    fetch('php/listar.php')
    .then(response => response.json())
    .then(data => {
        mostrarArchivos(data.archivos);
    })
    .catch(error => {
        console.error('Error al cargar archivos:', error);
    });
}

// Función para mostrar archivos en el DOM
function mostrarArchivos(archivos) {
    const contenedor = document.getElementById('archivosSubidos');
    
    if (archivos.length === 0) {
        contenedor.innerHTML = `
            <div class="col-12">
                <div class="empty-state">
                    <div style="font-size: 64px; opacity: 0.3;">📁</div>
                    <p>No hay archivos subidos</p>
                </div>
            </div>
        `;
        return;
    }
    
    contenedor.innerHTML = '';
    
    archivos.forEach(archivo => {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6 archivo-item';
        
        const extension = archivo.nombre.split('.').pop().toLowerCase();
        let contenidoPreview = '';
        
        if (extension === 'jpg' || extension === 'jpeg' || extension === 'png') {
            contenidoPreview = `<img src="archivos/${archivo.nombre}" class="archivo-preview" alt="${archivo.nombre}">`;
        } else if (extension === 'pdf') {
            contenidoPreview = `<div class="archivo-icon">📄</div>`;
        }
        
        col.innerHTML = `
            <div class="archivo-card" onclick="abrirArchivo('${archivo.nombre}')">
                <button class="btn btn-danger btn-sm btn-eliminar" onclick="event.stopPropagation(); eliminarArchivo('${archivo.nombre}')">
                    ✕
                </button>
                ${contenidoPreview}
                <div class="archivo-nombre">${archivo.nombre}</div>
                <div class="archivo-fecha">${archivo.fecha}</div>
            </div>
        `;
        
        contenedor.appendChild(col);
    });
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    const mensajeDiv = document.getElementById('mensaje');
    mensajeDiv.innerHTML = `
        <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alert = mensajeDiv.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                mensajeDiv.innerHTML = '';
            }, 150);
        }
    }, 5000);
}

// Función para abrir archivo
function abrirArchivo(nombre) {
    window.open('archivos/' + nombre, '_blank');
}

// Función para eliminar archivo
function eliminarArchivo(nombre) {
    if (!confirm('¿Estás seguro de eliminar este archivo?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('archivo', nombre);
    
    fetch('php/eliminar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarMensaje(data.message, 'success');
            cargarArchivos();
        } else {
            mostrarMensaje(data.message, 'danger');
        }
    })
    .catch(error => {
        mostrarMensaje('Error al eliminar el archivo: ' + error, 'danger');
    });
}