// Calculadora de IMC (Índice de Masa Corporal)

document.addEventListener('DOMContentLoaded', function() {
    const imcForm = document.getElementById('imcForm');
    
    if (imcForm) {
        imcForm.addEventListener('submit', function(e) {
            e.preventDefault();
            calcularIMC();
        });
    }
    
    // Botón Limpiar
    const btnLimpiar = document.getElementById('btnLimpiar');
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            limpiarCalculadora();
        });
    }
    
    // Validación de inputs numéricos
    const numericInputs = document.querySelectorAll('#peso, #altura');
    
    numericInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            // Permitir números, punto decimal, backspace, delete, tab
            const allowedKeys = ['0','1','2','3','4','5','6','7','8','9','.','Backspace','Delete','Tab'];
            
            if (!allowedKeys.includes(e.key)) {
                e.preventDefault();
            }
            
            // Evitar múltiples puntos decimales
            if (e.key === '.' && this.value.includes('.')) {
                e.preventDefault();
            }
        });
    });
});

function calcularIMC() {
    // Obtener valores del formulario
    const peso = parseFloat(document.getElementById('peso').value);
    const alturaCm = parseFloat(document.getElementById('altura').value);
    
    // Validar datos
    if (!peso || !alturaCm || peso <= 0 || alturaCm <= 0) {
        alert('Por favor, introduce valores válidos');
        return;
    }
    
    // Convertir altura de cm a metros
    const alturaM = alturaCm / 100;
    
    // Calcular IMC: peso (kg) / altura² (m)
    const imc = peso / (alturaM * alturaM);
    
    // Mostrar resultado
    mostrarResultado(imc);
}

function mostrarResultado(imc) {
    const resultado = document.getElementById('resultado');
    const imcValor = document.getElementById('imcValor');
    const imcCategoria = document.getElementById('imcCategoria');
    const imcDescripcion = document.getElementById('imcDescripcion');
    
    // Redondear IMC a 2 decimales
    const imcRedondeado = imc.toFixed(2);
    
    // Determinar categoría y descripción
    let categoria = '';
    let descripcion = '';
    let color = '';
    
    if (imc < 18.5) {
        categoria = 'Bajo peso';
        descripcion = 'Tu IMC indica que tienes bajo peso. Te recomendamos consultar con un nutricionista para alcanzar un peso saludable.';
        color = '#17a2b8';
    } else if (imc >= 18.5 && imc < 25) {
        categoria = 'Peso normal';
        descripcion = '¡Excelente! Tu IMC está en el rango saludable. Mantén tu estilo de vida activo y alimentación balanceada.';
        color = '#28a745';
    } else if (imc >= 25 && imc < 30) {
        categoria = 'Sobrepeso';
        descripcion = 'Tu IMC indica sobrepeso. Te recomendamos iniciar un programa de ejercicio y mejorar tus hábitos alimenticios.';
        color = '#ffc107';
    } else if (imc >= 30 && imc < 35) {
        categoria = 'Obesidad I';
        descripcion = 'Tu IMC indica obesidad grado I. Es importante que consultes con un médico y nutricionista para un plan personalizado.';
        color = '#fd7e14';
    } else if (imc >= 35 && imc < 40) {
        categoria = 'Obesidad II';
        descripcion = 'Tu IMC indica obesidad grado II. Te recomendamos consultar con un profesional de la salud urgentemente.';
        color = '#dc3545';
    } else {
        categoria = 'Obesidad III';
        descripcion = 'Tu IMC indica obesidad grado III. Es crucial que consultes con un médico especialista de inmediato.';
        color = '#bd2130';
    }
    
    // Mostrar resultados con animación
    resultado.style.display = 'none';
    
    setTimeout(function() {
        imcValor.textContent = imcRedondeado;
        imcCategoria.textContent = categoria;
        imcCategoria.style.color = color;
        imcCategoria.style.fontWeight = 'bold';
        imcCategoria.style.fontSize = '1.3rem';
        imcDescripcion.innerHTML = `<p>${descripcion}</p>`;
        
        resultado.style.display = 'block';
        resultado.style.opacity = '0';
        resultado.style.transform = 'translateY(20px)';
        
        // Animación de entrada
        setTimeout(function() {
            resultado.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            resultado.style.opacity = '1';
            resultado.style.transform = 'translateY(0)';
        }, 50);
        
        // Scroll suave al resultado
        resultado.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }, 100);
}

function limpiarCalculadora() {
    // Limpiar inputs
    document.getElementById('peso').value = '';
    document.getElementById('altura').value = '';
    
    // Ocultar resultado
    const resultado = document.getElementById('resultado');
    resultado.style.display = 'none';
}