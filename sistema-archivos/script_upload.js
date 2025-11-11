// Mostrar nombre del archivo seleccionado
document.getElementById('archivo').addEventListener('change', function(e) {
    const fileName = document.getElementById('fileName');
    const file = e.target.files[0];
    
    if (file) {
        // Mostrar nombre y tamaño del archivo
        const tamano = formatBytes(file.size);
        fileName.innerHTML = `
            <strong>${file.name}</strong><br>
            <small>Tamaño: ${tamano}</small>
        `;
        
        // Validar tamaño
        const maxSize = 10485760; // 10 MB
        if (file.size > maxSize) {
            alert('⚠️ El archivo es demasiado grande. Tamaño máximo: 10 MB');
            e.target.value = '';
            fileName.textContent = 'Seleccionar archivo';
        }
    } else {
        fileName.textContent = 'Seleccionar archivo';
    }
});

// Función para formatear bytes
function formatBytes(bytes, decimals = 2) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

// Prevenir envío múltiple del formulario
let formSubmitted = false;

// Validar formulario antes de enviar
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const archivo = document.getElementById('archivo').files[0];
    
    if (!archivo) {
        e.preventDefault();
        alert('⚠️ Por favor, selecciona un archivo');
        return false;
    }
    
    // Prevenir envío múltiple
    if (formSubmitted) {
        e.preventDefault();
        alert('⏳ El archivo se está subiendo, por favor espera...');
        return false;
    }
    
    formSubmitted = true;
    
    // Mostrar indicador de carga con progreso
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    
    let progress = 0;
    const interval = setInterval(() => {
        progress += 10;
        if (progress <= 90) {
            btn.innerHTML = `<span>⏳ Subiendo... ${progress}%</span>`;
        }
        if (progress >= 90) {
            clearInterval(interval);
            btn.innerHTML = '<span>⏳ Finalizando...</span>';
        }
    }, 300);
    
    // Restaurar botón después de 30 segundos (por si hay error)
    setTimeout(() => {
        clearInterval(interval);
        btn.innerHTML = originalText;
        btn.disabled = false;
        formSubmitted = false;
    }, 30000);
});

// Confirmar eliminación
function confirmarEliminar(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el archivo "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        window.location.href = `?eliminar=${id}`;
    }
}

// Ocultar mensaje después de 5 segundos
window.addEventListener('load', function() {
    const mensaje = document.getElementById('mensaje');
    if (mensaje) {
        setTimeout(() => {
            mensaje.style.transition = 'opacity 0.5s ease';
            mensaje.style.opacity = '0';
            setTimeout(() => {
                mensaje.style.display = 'none';
            }, 500);
        }, 5000);
    }
});

// Drag and drop
const fileLabel = document.querySelector('.file-label');
const fileInput = document.getElementById('archivo');

// Prevenir comportamiento por defecto
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    fileLabel.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Resaltar área cuando se arrastra un archivo
['dragenter', 'dragover'].forEach(eventName => {
    fileLabel.addEventListener(eventName, () => {
        fileLabel.style.borderColor = '#764ba2';
        fileLabel.style.background = '#f0f0f0';
    }, false);
});

['dragleave', 'drop'].forEach(eventName => {
    fileLabel.addEventListener(eventName, () => {
        fileLabel.style.borderColor = '#667eea';
        fileLabel.style.background = 'white';
    }, false);
});

// Manejar archivo arrastrado
fileLabel.addEventListener('drop', function(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        fileInput.files = files;
        // Disparar evento change
        const event = new Event('change', { bubbles: true });
        fileInput.dispatchEvent(event);
    }
}, false);

// Validación en tiempo real del tipo de archivo
fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/zip',
            'application/x-rar-compressed',
            'application/x-zip-compressed'
        ];
        
        if (!allowedTypes.includes(file.type)) {
            alert('⚠️ Tipo de archivo no permitido.\n\nSolo se permiten: Imágenes (JPG, PNG, GIF, WEBP), PDF, Word, Excel, TXT, ZIP, RAR');
            e.target.value = '';
            document.getElementById('fileName').textContent = 'Seleccionar archivo';
        }
    }
});

// Confirmación antes de salir si hay datos en el formulario
let formularioModificado = false;

document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="file"], textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            formularioModificado = true;
        });
        input.addEventListener('change', function() {
            formularioModificado = true;
        });
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formularioModificado && !formSubmitted) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Resetear formulario después de subir
window.addEventListener('load', function() {
    // Si la página se recargó después de subir, resetear el estado
    formSubmitted = false;
    formularioModificado = false;
});