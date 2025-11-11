// Control del reproductor de video
const videoPlayer = document.getElementById('videoPlayer');

if(videoPlayer) {
    // Guardar tiempo de reproducción
    let saveProgressInterval;
    
    videoPlayer.addEventListener('play', () => {
        saveProgressInterval = setInterval(() => {
            const currentTime = videoPlayer.currentTime;
            const duration = videoPlayer.duration;
            const progress = (currentTime / duration) * 100;
            
            // Guardar progreso en localStorage
            const videoId = new URLSearchParams(window.location.search).get('v');
            if(videoId) {
                localStorage.setItem(`video_${videoId}_progress`, currentTime);
            }
        }, 5000);
    });
    
    videoPlayer.addEventListener('pause', () => {
        clearInterval(saveProgressInterval);
    });
    
    videoPlayer.addEventListener('ended', () => {
        clearInterval(saveProgressInterval);
        
        // Marcar video como visto completamente
        const videoId = new URLSearchParams(window.location.search).get('v');
        if(videoId) {
            localStorage.setItem(`video_${videoId}_completed`, 'true');
        }
    });
    
    // Restaurar progreso al cargar
    window.addEventListener('load', () => {
        const videoId = new URLSearchParams(window.location.search).get('v');
        if(videoId) {
            const savedProgress = localStorage.getItem(`video_${videoId}_progress`);
            if(savedProgress && !localStorage.getItem(`video_${videoId}_completed`)) {
                videoPlayer.currentTime = parseFloat(savedProgress);
            }
        }
    });
    
    // Controles personalizados adicionales
    let isFullscreen = false;
    
    // Doble clic para pantalla completa
    videoPlayer.addEventListener('dblclick', () => {
        if(!isFullscreen) {
            if(videoPlayer.requestFullscreen) {
                videoPlayer.requestFullscreen();
            } else if(videoPlayer.webkitRequestFullscreen) {
                videoPlayer.webkitRequestFullscreen();
            } else if(videoPlayer.msRequestFullscreen) {
                videoPlayer.msRequestFullscreen();
            }
            isFullscreen = true;
        } else {
            if(document.exitFullscreen) {
                document.exitFullscreen();
            } else if(document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if(document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            isFullscreen = false;
        }
    });
    
    // Atajos de teclado
    document.addEventListener('keydown', (e) => {
        if(!videoPlayer.paused || e.key === ' ') {
            switch(e.key) {
                case ' ': // Espacio - Play/Pause
                    e.preventDefault();
                    if(videoPlayer.paused) {
                        videoPlayer.play();
                    } else {
                        videoPlayer.pause();
                    }
                    break;
                    
                case 'ArrowLeft': // Flecha izquierda - Retroceder 5s
                    e.preventDefault();
                    videoPlayer.currentTime = Math.max(0, videoPlayer.currentTime - 5);
                    break;
                    
                case 'ArrowRight': // Flecha derecha - Avanzar 5s
                    e.preventDefault();
                    videoPlayer.currentTime = Math.min(videoPlayer.duration, videoPlayer.currentTime + 5);
                    break;
                    
                case 'ArrowUp': // Flecha arriba - Subir volumen
                    e.preventDefault();
                    videoPlayer.volume = Math.min(1, videoPlayer.volume + 0.1);
                    break;
                    
                case 'ArrowDown': // Flecha abajo - Bajar volumen
                    e.preventDefault();
                    videoPlayer.volume = Math.max(0, videoPlayer.volume - 0.1);
                    break;
                    
                case 'm': // M - Mutear/Desmutear
                    videoPlayer.muted = !videoPlayer.muted;
                    break;
                    
                case 'f': // F - Pantalla completa
                    if(!isFullscreen) {
                        videoPlayer.requestFullscreen();
                        isFullscreen = true;
                    } else {
                        document.exitFullscreen();
                        isFullscreen = false;
                    }
                    break;
            }
        }
    });
    
    // Mostrar indicador de velocidad de reproducción
    videoPlayer.addEventListener('ratechange', () => {
        const rate = videoPlayer.playbackRate;
        showNotification(`Velocidad: ${rate}x`);
    });
    
    // Mostrar indicador de volumen
    videoPlayer.addEventListener('volumechange', () => {
        if(videoPlayer.muted) {
            showNotification('Silenciado');
        } else {
            const volume = Math.round(videoPlayer.volume * 100);
            showNotification(`Volumen: ${volume}%`);
        }
    });
}

// Función para mostrar notificaciones temporales
function showNotification(message) {
    const existing = document.querySelector('.video-notification');
    if(existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'video-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 16px 24px;
        border-radius: 8px;
        font-size: 18px;
        z-index: 9999;
        pointer-events: none;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s';
        setTimeout(() => notification.remove(), 300);
    }, 1000);
}

// Picture-in-Picture
const pipBtn = document.querySelector('.pip-btn');
if(pipBtn && videoPlayer) {
    pipBtn.addEventListener('click', async () => {
        try {
            if(document.pictureInPictureElement) {
                await document.exitPictureInPicture();
            } else {
                await videoPlayer.requestPictureInPicture();
            }
        } catch(error) {
            console.error('Error con Picture-in-Picture:', error);
        }
    });
}

// Autoplay del siguiente video
videoPlayer?.addEventListener('ended', () => {
    const nextVideo = document.querySelector('.related-video');
    if(nextVideo) {
        const confirmNext = confirm('¿Reproducir el siguiente video?');
        if(confirmNext) {
            const nextLink = nextVideo.querySelector('a');
            if(nextLink) {
                window.location.href = nextLink.href;
            }
        }
    }
});

// Compartir video
function shareVideo() {
    const videoUrl = window.location.href;
    const videoTitle = document.querySelector('.video-title').textContent;
    
    if (navigator.share) {
        navigator.share({
            title: videoTitle,
            url: videoUrl
        }).catch(err => {
            // Si falla, copiar al portapapeles
            copyToClipboard(videoUrl);
        });
    } else {
        copyToClipboard(videoUrl);
    }
}

// Copiar al portapapeles
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('✓ Enlace copiado al portapapeles');
        }).catch(() => {
            // Método alternativo
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

// Método alternativo para copiar
function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showNotification('✓ Enlace copiado al portapapeles');
    } catch (err) {
        showNotification('✗ Error al copiar enlace');
    }
    document.body.removeChild(textArea);
}

// Descargar video
function downloadVideo() {
    const videoPlayer = document.getElementById('videoPlayer');
    if(videoPlayer) {
        const videoSrc = videoPlayer.querySelector('source').src;
        const videoTitle = document.querySelector('.video-title').textContent;
        
        const a = document.createElement('a');
        a.href = videoSrc;
        a.download = videoTitle + '.mp4';
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showNotification('✓ Descargando video...');
    } else {
        showNotification('✗ Error al descargar video');
    }
}

// Guardar video en una lista
function toggleSaveVideo() {
    const saveBtn = document.getElementById('saveBtn');
    const icon = saveBtn.querySelector('i');
    
    if(icon.classList.contains('fa-plus')) {
        icon.classList.remove('fa-plus');
        icon.classList.add('fa-check');
        saveBtn.classList.add('active');
        showNotification('✓ Video guardado');
    } else {
        icon.classList.remove('fa-check');
        icon.classList.add('fa-plus');
        saveBtn.classList.remove('active');
        showNotification('Video eliminado de guardados');
    }
}

// Agregar a ver más tarde
function addToWatchLater(videoId) {
    // Guardar en localStorage
    let watchLater = JSON.parse(localStorage.getItem('watchLater') || '[]');
    
    if(!watchLater.includes(videoId)) {
        watchLater.push(videoId);
        localStorage.setItem('watchLater', JSON.stringify(watchLater));
        showNotification('✓ Agregado a "Ver más tarde"');
    } else {
        watchLater = watchLater.filter(id => id !== videoId);
        localStorage.setItem('watchLater', JSON.stringify(watchLater));
        showNotification('Eliminado de "Ver más tarde"');
    }
}