<header class="header">
    <div class="header-left">
        <button class="menu-btn" id="menuBtn">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="index.php" class="logo">
            <i class="fa-brands fa-youtube"></i>
            <span>YouTube</span>
        </a>
    </div>
    
    <div class="header-center">
        <form action="search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Buscar" class="search-input" required>
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
        <button class="voice-search-btn" onclick="voiceSearch()" title="Buscar por voz">
            <i class="fa-solid fa-microphone"></i>
        </button>
    </div>
    
    <div class="header-right">
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="upload.php" class="icon-btn" title="Subir video">
            <i class="fa-solid fa-video"></i>
        </a>
        <?php else: ?>
        <button class="icon-btn" onclick="window.location.href='login.php'" title="Iniciar sesión para subir">
            <i class="fa-solid fa-video"></i>
        </button>
        <?php endif; ?>
        
        <button class="icon-btn" onclick="showAppsMenu()" title="Aplicaciones de YouTube">
            <i class="fa-solid fa-grip"></i>
        </button>
        
        <button class="icon-btn" onclick="showNotifications()" title="Notificaciones">
            <i class="fa-solid fa-bell"></i>
            <?php if(isset($_SESSION['user_id'])): ?>
            <span class="notification-badge" id="notificationBadge">3</span>
            <?php endif; ?>
        </button>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="user-menu">
                <button class="user-avatar" id="userMenuBtn">
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar'] ?? 'assets/images/default-avatar.png'); ?>" alt="Avatar">
                </button>
                <div class="dropdown-menu" id="userDropdown">
                    <a href="channel.php?id=<?php echo $_SESSION['user_id']; ?>">
                        <i class="fas fa-user"></i> Tu canal
                    </a>
                    <a href="upload.php">
                        <i class="fas fa-upload"></i> Subir video
                    </a>
                    <a href="studio.php">
                        <i class="fas fa-play-circle"></i> YouTube Studio
                    </a>
                    <hr>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php" class="login-btn">
                <i class="fas fa-user-circle"></i> Iniciar sesión
            </a>
        <?php endif; ?>
    </div>
    
    <!-- Apps Menu Dropdown -->
    <div class="apps-dropdown" id="appsDropdown">
        <div class="dropdown-header">
            <h3>Aplicaciones de YouTube</h3>
        </div>
        <div class="apps-grid">
            <a href="index.php" class="app-item">
                <i class="fab fa-youtube"></i>
                <span>YouTube</span>
            </a>
            <?php if(isset($_SESSION['user_id'])): ?>
            <a href="studio.php" class="app-item">
                <i class="fas fa-play-circle"></i>
                <span>Studio</span>
            </a>
            <?php else: ?>
            <a href="login.php" class="app-item">
                <i class="fas fa-play-circle"></i>
                <span>Studio</span>
            </a>
            <?php endif; ?>
            <a href="category.php?c=music" class="app-item">
                <i class="fas fa-music"></i>
                <span>Music</span>
            </a>
            <a href="index.php" class="app-item" onclick="alert('YouTube Kids estará disponible próximamente'); return false;">
                <i class="fas fa-child"></i>
                <span>Kids</span>
            </a>
        </div>
    </div>
    
    <!-- Notifications Dropdown -->
    <div class="notifications-dropdown" id="notificationsDropdown">
        <div class="dropdown-header">
            <h3>Notificaciones</h3>
            <button onclick="clearNotificationBadge()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 12px;">
                Marcar como leídas
            </button>
        </div>
        <div class="notifications-list">
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="notification-item" onclick="window.location.href='index.php'">
                <img src="assets/images/default-avatar.png" alt="" class="notification-avatar">
                <div class="notification-content">
                    <p><strong>Bienvenido a YouTube Clone</strong></p>
                    <p>Gracias por unirte a nuestra plataforma</p>
                    <p class="notification-time">Hace 1 hora</p>
                </div>
            </div>
            <div class="notification-item" onclick="window.location.href='trending.php'">
                <img src="assets/images/default-avatar.png" alt="" class="notification-avatar">
                <div class="notification-content">
                    <p><strong>Explora contenido nuevo</strong></p>
                    <p>Descubre videos populares en tendencias</p>
                    <p class="notification-time">Hace 3 horas</p>
                </div>
            </div>
            <div class="notification-item" onclick="window.location.href='studio.php'">
                <img src="assets/images/default-avatar.png" alt="" class="notification-avatar">
                <div class="notification-content">
                    <p><strong>Personaliza tu canal</strong></p>
                    <p>Ve a Studio para configurar tu canal</p>
                    <p class="notification-time">Hace 1 día</p>
                </div>
            </div>
            <?php else: ?>
            <div class="empty-notifications">
                <i class="fas fa-bell-slash"></i>
                <p>No tienes notificaciones</p>
                <p style="font-size: 12px; margin-top: 8px;">Inicia sesión para ver tus notificaciones</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<style>
    .notification-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        background-color: var(--primary-color);
        color: white;
        font-size: 10px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 10px;
        min-width: 18px;
        text-align: center;
    }
    
    .icon-btn {
        position: relative;
    }
    
    .apps-dropdown, .notifications-dropdown {
        display: none;
        position: fixed;
        top: 60px;
        right: 80px;
        background-color: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        width: 300px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        z-index: 1001;
        max-height: 400px;
        overflow-y: auto;
    }
    
    .apps-dropdown.show, .notifications-dropdown.show {
        display: block;
    }
    
    .dropdown-header {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .dropdown-header h3 {
        font-size: 16px;
        font-weight: 500;
    }
    
    .apps-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        padding: 16px;
    }
    
    .app-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 16px;
        color: var(--text-primary);
        text-decoration: none;
        border-radius: 8px;
        transition: background-color 0.2s;
    }
    
    .app-item:hover {
        background-color: var(--hover-bg);
    }
    
    .app-item i {
        font-size: 24px;
    }
    
    .app-item span {
        font-size: 12px;
    }
    
    .notifications-list {
        padding: 8px 0;
    }
    
    .notification-item {
        display: flex;
        gap: 12px;
        padding: 12px 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        color: var(--text-primary);
    }
    
    .notification-item:hover {
        background-color: var(--hover-bg);
    }
    
    .notification-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-content p {
        font-size: 14px;
        line-height: 1.4;
    }
    
    .notification-time {
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 4px;
    }
    
    .empty-notifications {
        text-align: center;
        padding: 40px 20px;
        color: var(--text-secondary);
    }
    
    .empty-notifications i {
        font-size: 48px;
        margin-bottom: 16px;
        display: block;
    }
</style>

<script>
    // Búsqueda por voz
    function voiceSearch() {
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            recognition.lang = 'es-ES';
            
            recognition.onstart = function() {
                document.querySelector('.voice-search-btn').style.color = '#ff0000';
            };
            
            recognition.onend = function() {
                document.querySelector('.voice-search-btn').style.color = '';
            };
            
            recognition.onresult = function(event) {
                const searchInput = document.querySelector('.search-input');
                searchInput.value = event.results[0][0].transcript;
                document.querySelector('.search-form').submit();
            };
            
            recognition.onerror = function() {
                alert('Error al usar la búsqueda por voz. Verifica los permisos del micrófono.');
            };
            
            recognition.start();
        } else {
            alert('Tu navegador no soporta búsqueda por voz. Prueba con Chrome o Edge.');
        }
    }
    
    // Mostrar menú de aplicaciones
    function showAppsMenu() {
        const appsDropdown = document.getElementById('appsDropdown');
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        
        // Cerrar notificaciones si están abiertas
        notificationsDropdown.classList.remove('show');
        
        // Toggle del menú de apps
        appsDropdown.classList.toggle('show');
    }
    
    // Mostrar notificaciones
    function showNotifications() {
        const appsDropdown = document.getElementById('appsDropdown');
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        const badge = document.getElementById('notificationBadge');
        
        // Cerrar apps si están abiertas
        appsDropdown.classList.remove('show');
        
        // Toggle de notificaciones
        notificationsDropdown.classList.toggle('show');
        
        // Si se abre el panel, ocultar badge automáticamente
        if(notificationsDropdown.classList.contains('show') && badge) {
            setTimeout(() => {
                badge.style.display = 'none';
                localStorage.setItem('notificationsRead', 'true');
            }, 500);
        }
    }
    
    // Limpiar badge de notificaciones
    function clearNotificationBadge() {
        const badge = document.getElementById('notificationBadge');
        if(badge) {
            badge.style.display = 'none';
            localStorage.setItem('notificationsRead', 'true');
        }
    }
    
    // Verificar si las notificaciones ya fueron leídas
    window.addEventListener('DOMContentLoaded', function() {
        const notificationsRead = localStorage.getItem('notificationsRead');
        const badge = document.getElementById('notificationBadge');
        if(notificationsRead === 'true' && badge) {
            badge.style.display = 'none';
        }
    });
    
    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(event) {
        const appsDropdown = document.getElementById('appsDropdown');
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        
        if (!event.target.closest('.icon-btn') && 
            !event.target.closest('.apps-dropdown') && 
            !event.target.closest('.notifications-dropdown')) {
            appsDropdown.classList.remove('show');
            notificationsDropdown.classList.remove('show');
        }
    });
</script>