// JavaScript para el formulario de contacto

document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarFormulario();
        });
    }
    
    // Validación en tiempo real del email
    const emailInput = document.getElementById('email');
    
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !validarEmail(email)) {
                this.style.borderColor = '#dc3545';
                // Crear mensaje de error si no existe
                let errorMsg = this.nextElementSibling;
                if (!errorMsg || !errorMsg.classList.contains('error-msg')) {
                    errorMsg = document.createElement('span');
                    errorMsg.classList.add('error-msg');
                    errorMsg.style.color = '#dc3545';
                    errorMsg.style.fontSize = '14px';
                    errorMsg.textContent = 'Email no válido';
                    this.parentNode.appendChild(errorMsg);
                }
            } else {
                this.style.borderColor = '#ddd';
                // Eliminar mensaje de error si existe
                const errorMsg = this.nextElementSibling;
                if (errorMsg && errorMsg.classList.contains('error-msg')) {
                    errorMsg.remove();
                }
            }
        });
    }
});

function enviarFormulario() {
    const form = document.getElementById('contactForm');
    const mensajeRespuesta = document.getElementById('mensajeRespuesta');
    
    // Obtener datos del formulario
    const formData = new FormData(form);
    
    // Validar datos
    if (!validarFormulario(formData)) {
        return;
    }
    
    // Deshabilitar botón de envío
    const submitBtn = form.querySelector('button[type="submit"]');
    const textoOriginal = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviando...';
    
    // Enviar datos por AJAX
    fetch('php/procesar_contacto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Mostrar mensaje de respuesta
        mostrarMensaje(data.success, data.message);
        
        // Si el envío fue exitoso, limpiar formulario
        if (data.success) {
            form.reset();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensaje(false, 'Hubo un error al enviar el mensaje. Por favor, intenta de nuevo.');
    })
    .finally(() => {
        // Rehabilitar botón
        submitBtn.disabled = false;
        submitBtn.textContent = textoOriginal;
    });
}

function validarFormulario(formData) {
    const nombre = formData.get('nombre').trim();
    const email = formData.get('email').trim();
    const asunto = formData.get('asunto').trim();
    const mensaje = formData.get('mensaje').trim();
    
    if (!nombre) {
        alert('Por favor, introduce tu nombre');
        return false;
    }
    
    if (!email || !validarEmail(email)) {
        alert('Por favor, introduce un email válido');
        return false;
    }
    
    if (!asunto) {
        alert('Por favor, introduce un asunto');
        return false;
    }
    
    if (!mensaje) {
        alert('Por favor, escribe tu mensaje');
        return false;
    }
    
    if (mensaje.length < 10) {
        alert('El mensaje debe tener al menos 10 caracteres');
        return false;
    }
    
    return true;
}

function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function mostrarMensaje(exito, mensaje) {
    const mensajeRespuesta = document.getElementById('mensajeRespuesta');
    
    // Limpiar clases anteriores
    mensajeRespuesta.classList.remove('exito', 'error');
    
    // Añadir clase según el resultado
    if (exito) {
        mensajeRespuesta.classList.add('exito');
    } else {
        mensajeRespuesta.classList.add('error');
    }
    
    // Mostrar mensaje
    mensajeRespuesta.textContent = mensaje;
    mensajeRespuesta.style.display = 'block';
    
    // Scroll al mensaje
    mensajeRespuesta.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Ocultar mensaje después de 5 segundos
    setTimeout(function() {
        mensajeRespuesta.style.opacity = '0';
        setTimeout(function() {
            mensajeRespuesta.style.display = 'none';
            mensajeRespuesta.style.opacity = '1';
        }, 500);
    }, 5000);
}