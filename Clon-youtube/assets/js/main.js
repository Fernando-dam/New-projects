// Toggle sidebar
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.getElementById('sidebar');

if(menuBtn && sidebar) {
    console.log('Menú lateral inicializado correctamente');
    
    menuBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Click en menú lateral');
        sidebar.classList.toggle('show');
        
        // Guardar estado en localStorage
        if(sidebar.classList.contains('show')) {
            localStorage.setItem('sidebarOpen', 'true');
        } else {
            localStorage.setItem('sidebarOpen', 'false');
        }
    });
    
    // Restaurar estado del sidebar
    const sidebarOpen = localStorage.getItem('sidebarOpen');
    if(sidebarOpen === 'true' && window.innerWidth <= 1024) {
        sidebar.classList.add('show');
    }
    
    // Cerrar sidebar al hacer clic fuera en móvil
    document.addEventListener('click', function(e) {
        if(window.innerWidth <= 1024) {
            if(!sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                sidebar.classList.remove('show');
                localStorage.setItem('sidebarOpen', 'false');
            }
        }
    });
    
    // Prevenir que los clicks dentro del sidebar lo cierren
    sidebar.addEventListener('click', function(e) {
        e.stopPropagation();
    });
}

// Toggle user dropdown
const userMenuBtn = document.getElementById('userMenuBtn');
const userDropdown = document.getElementById('userDropdown');

if(userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('show');
    });
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', () => {
        userDropdown.classList.remove('show');
    });
}

// Función para dar like/dislike a videos
async function likeVideo(videoId, type) {
    try {
        const response = await fetch('api/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                video_id: videoId,
                type: type
            })
        });
        
        const data = await response.json();
        
        if(data.success) {
            document.getElementById('likes-count').textContent = formatNumber(data.likes);
            document.getElementById('dislikes-count').textContent = formatNumber(data.dislikes);
            
            // Actualizar estado de botones
            const likeBtn = document.querySelector('.action-btn:nth-of-type(1)');
            const dislikeBtn = document.querySelector('.action-btn:nth-of-type(2)');
            
            likeBtn.classList.remove('active');
            dislikeBtn.classList.remove('active');
            
            if(type === 'like') {
                likeBtn.classList.add('active');
            } else {
                dislikeBtn.classList.add('active');
            }
        } else {
            if(data.message === 'No autenticado') {
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
}

// Función para suscribirse/desuscribirse
async function toggleSubscribe(channelId) {
    try {
        const response = await fetch('api/subscribe.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                channel_id: channelId
            })
        });
        
        const data = await response.json();
        
        if(data.success) {
            const subscribeBtn = document.getElementById('subscribeBtn') || document.querySelector('.subscribe-btn, .subscribe-btn-large');
            const subscribeText = subscribeBtn.querySelector('span, .subscribe-text');
            const subscribersCount = document.getElementById('subscribersCount') || document.querySelector('.subscribers-count');
            
            if(data.subscribed) {
                subscribeBtn.classList.add('subscribed');
                subscribeText.textContent = 'Suscrito';
                
                // Mostrar notificación
                if(typeof showNotification === 'function') {
                    showNotification('✓ Te has suscrito al canal');
                }
            } else {
                subscribeBtn.classList.remove('subscribed');
                subscribeText.textContent = 'Suscribirse';
                
                if(typeof showNotification === 'function') {
                    showNotification('Has cancelado la suscripción');
                }
            }
            
            // Actualizar contador de suscriptores si existe
            if(subscribersCount) {
                subscribersCount.textContent = formatNumber(data.subscribers) + ' suscriptores';
            }
        } else {
            if(data.message === 'No autenticado') {
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    }
}

// Función para formatear números
function formatNumber(num) {
    if(num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if(num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

// Función para publicar comentarios
async function postComment(event, videoId) {
    event.preventDefault();
    
    const commentInput = document.getElementById('comment-input');
    const comment = commentInput.value.trim();
    
    if(!comment) return;
    
    try {
        const response = await fetch('api/comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                video_id: videoId,
                comment: comment
            })
        });
        
        const data = await response.json();
        
        if(data.success) {
            // Añadir comentario a la lista
            const commentsList = document.getElementById('comments-list');
            const commentHTML = `
                <div class="comment">
                    <img src="${data.comment.channel_avatar}" alt="" class="comment-avatar">
                    <div class="comment-content">
                        <div class="comment-header">
                            <span class="comment-author">${data.comment.username}</span>
                            <span class="comment-date">Ahora</span>
                        </div>
                        <p class="comment-text">${data.comment.comment}</p>
                        <div class="comment-actions">
                            <button class="comment-btn">
                                <i class="fas fa-thumbs-up"></i>
                                <span>0</span>
                            </button>
                            <button class="comment-btn">
                                <i class="fas fa-thumbs-down"></i>
                            </button>
                            <button class="comment-btn">Responder</button>
                        </div>
                    </div>
                </div>
            `;
            
            commentsList.insertAdjacentHTML('afterbegin', commentHTML);
            commentInput.value = '';
            
            // Actualizar contador de comentarios
            const commentsSection = document.querySelector('.comments-section h3');
            if(commentsSection) {
                const currentCount = parseInt(commentsSection.textContent);
                commentsSection.textContent = (currentCount + 1) + ' comentarios';
            }
        } else {
            if(data.message === 'No autenticado') {
                window.location.href = 'login.php';
            } else {
                alert(data.message);
            }
        }
    } catch(error) {
        console.error('Error:', error);
        alert('Error al publicar el comentario');
    }
}

// Auto-resize del textarea de comentarios
const commentTextareas = document.querySelectorAll('textarea');
commentTextareas.forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

// Confirmación antes de salir si hay cambios sin guardar
let formModified = false;
const forms = document.querySelectorAll('form');

forms.forEach(form => {
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            formModified = true;
        });
    });
    
    form.addEventListener('submit', () => {
        formModified = false;
    });
});

window.addEventListener('beforeunload', (e) => {
    if(formModified) {
        e.preventDefault();
        e.returnValue = '';
    }
});