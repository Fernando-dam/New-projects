// JavaScript principal para el sitio web del gimnasio

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar funcionalidades
    initSearchBar();
    initMobileMenu();
    initFooterEmail();
    animateOnScroll();
});

// Barra de búsqueda
function initSearchBar() {
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-bar input');

    if (searchBtn && searchInput) {
        searchBtn.addEventListener('click', function() {
            const searchTerm = searchInput.value.trim();
            if (searchTerm) {
                alert('Buscando: ' + searchTerm);
                // Aquí se implementaría la funcionalidad de búsqueda real
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = searchInput.value.trim();
                if (searchTerm) {
                    alert('Buscando: ' + searchTerm);
                }
            }
        });
    }
}

// Menú móvil
function initMobileMenu() {
    const menuBtn = document.querySelector('.menu-btn');
    const nav = document.querySelector('.nav');

    if (menuBtn && nav) {
        menuBtn.addEventListener('click', function() {
            nav.classList.toggle('active');
            
            // Cambiar ícono del menú
            if (nav.classList.contains('active')) {
                menuBtn.textContent = '✕';
                nav.style.display = 'flex';
            } else {
                menuBtn.textContent = '☰';
                if (window.innerWidth <= 768) {
                    nav.style.display = 'none';
                }
            }
        });
    }
}

// Email del footer
function initFooterEmail() {
    const footerEmailBtn = document.querySelector('.footer-contact .btn-submit');
    const footerEmailInput = document.querySelector('.footer-contact input[type="email"]');

    if (footerEmailBtn && footerEmailInput) {
        footerEmailBtn.addEventListener('click', function() {
            suscribirNewsletter();
        });
    }
}

// Función para suscribir al newsletter
function suscribirNewsletter() {
    const footerEmailInput = document.getElementById('footerEmail');
    if (!footerEmailInput) return;
    
    const email = footerEmailInput.value.trim();
    
    if (email && validateEmail(email)) {
        // Aquí se enviaría el email a través de AJAX
        alert('¡Gracias por suscribirte! Te hemos enviado un email a: ' + email);
        footerEmailInput.value = '';
    } else {
        alert('Por favor, introduce un email válido');
    }
}

// Validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Animaciones al hacer scroll
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1
    });

    // Observar elementos para animación
    const animatedElements = document.querySelectorAll('.service-card, .feature-card, .content-section');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Botón "Toma esta oferta"
const clientBtn = document.querySelector('.client-btn');
if (clientBtn) {
    clientBtn.addEventListener('click', function() {
        alert('¡Oferta especial! Contáctanos para más información');
        window.location.href = 'contacto.html';
    });
}

// Botón de usuario
const userBtn = document.querySelector('.user-btn');
if (userBtn) {
    userBtn.addEventListener('click', function() {
        alert('Funcionalidad de usuario - Próximamente');
    });
}

// Smooth scroll para enlaces internos
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Función para mostrar planes (modal)
function mostrarPlanes() {
    const planesHTML = `
        <div id="modalPlanes" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close" onclick="cerrarModal()">&times;</span>
                <h2>Nuestros Planes</h2>
                <div class="planes-grid">
                    <div class="plan-card">
                        <h3>Plan Básico</h3>
                        <p class="precio">29.99€<span>/mes</span></p>
                        <ul class="plan-features">
                            <li>✓ Acceso al gimnasio</li>
                            <li>✓ Horario completo</li>
                            <li>✓ Vestuarios y duchas</li>
                            <li>✓ Área de cardio</li>
                        </ul>
                        <button class="btn-plan">Elegir Plan</button>
                    </div>
                    
                    <div class="plan-card destacado">
                        <div class="badge">Más Popular</div>
                        <h3>Plan Premium</h3>
                        <p class="precio">49.99€<span>/mes</span></p>
                        <ul class="plan-features">
                            <li>✓ Todo del Plan Básico</li>
                            <li>✓ Clases grupales ilimitadas</li>
                            <li>✓ Zona de pesas</li>
                            <li>✓ Sauna y spa</li>
                            <li>✓ Nutricionista 1 vez/mes</li>
                        </ul>
                        <button class="btn-plan">Elegir Plan</button>
                    </div>
                    
                    <div class="plan-card">
                        <h3>Plan Elite</h3>
                        <p class="precio">89.99€<span>/mes</span></p>
                        <ul class="plan-features">
                            <li>✓ Todo del Plan Premium</li>
                            <li>✓ Entrenador personal</li>
                            <li>✓ Plan nutricional personalizado</li>
                            <li>✓ Acceso prioritario</li>
                            <li>✓ Invitado gratis 4 veces/mes</li>
                        </ul>
                        <button class="btn-plan">Elegir Plan</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', planesHTML);
    
    // Añadir event listeners a los botones de plan
    document.querySelectorAll('.btn-plan').forEach(btn => {
        btn.addEventListener('click', function() {
            alert('¡Excelente elección! Contacta con nosotros para inscribirte.');
            window.location.href = 'contacto.html';
        });
    });
}

// Función para cerrar modal
function cerrarModal() {
    const modal = document.getElementById('modalPlanes');
    if (modal) {
        modal.remove();
    }
}