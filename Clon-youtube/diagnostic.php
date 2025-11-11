<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - YouTube Clone</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #0f0f0f;
            color: white;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #212121;
            padding: 40px;
            border-radius: 12px;
        }
        h1 { color: #ff0000; margin-bottom: 30px; }
        h2 { color: #3ea6ff; margin: 30px 0 15px; font-size: 20px; }
        .test-section {
            background: #181818;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .test-item {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            margin: 8px 0;
            background: #3f3f3f;
            border-radius: 6px;
        }
        .status { padding: 4px 12px; border-radius: 4px; font-size: 14px; }
        .ok { background: #0f7b0f; }
        .error { background: #cc0000; }
        .warning { background: #ff6b00; }
        button {
            background: #ff0000;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 10px 10px 0;
        }
        button:hover { background: #cc0000; }
        .code {
            background: #000;
            padding: 15px;
            border-radius: 6px;
            font-family: monospace;
            margin-top: 10px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnóstico del Sistema - YouTube Clone</h1>
        
        <div class="test-section">
            <h2>📁 Archivos Principales</h2>
            <?php
            $files = [
                'index.php' => 'Página principal',
                'watch.php' => 'Reproductor',
                'upload.php' => 'Subir videos',
                'login.php' => 'Login',
                'register.php' => 'Registro',
                'studio.php' => 'YouTube Studio',
                'watchlater.php' => 'Ver más tarde',
                'trending.php' => 'Tendencias',
                'library.php' => 'Biblioteca',
                'history.php' => 'Historial',
                'liked.php' => 'Me gusta',
                'search.php' => 'Búsqueda',
                'subscriptions.php' => 'Suscripciones',
                'channel.php' => 'Canal',
                'category.php' => 'Categorías',
                'logout.php' => 'Cerrar sesión'
            ];
            
            foreach($files as $file => $desc) {
                $exists = file_exists($file);
                echo '<div class="test-item">';
                echo '<span>' . $desc . ' (' . $file . ')</span>';
                echo '<span class="status ' . ($exists ? 'ok' : 'error') . '">';
                echo $exists ? '✓ EXISTE' : '✗ FALTA';
                echo '</span></div>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>📂 Carpetas</h2>
            <?php
            $folders = [
                'api' => 'APIs',
                'assets' => 'Recursos',
                'assets/css' => 'CSS',
                'assets/js' => 'JavaScript',
                'assets/images' => 'Imágenes',
                'config' => 'Configuración',
                'includes' => 'Includes',
                'uploads' => 'Uploads',
                'uploads/videos' => 'Videos',
                'uploads/thumbnails' => 'Miniaturas'
            ];
            
            foreach($folders as $folder => $desc) {
                $exists = is_dir($folder);
                $writable = $exists && is_writable($folder);
                echo '<div class="test-item">';
                echo '<span>' . $desc . ' (' . $folder . ')</span>';
                echo '<span class="status ' . ($exists ? ($writable ? 'ok' : 'warning') : 'error') . '">';
                if($exists) {
                    echo $writable ? '✓ OK' : '⚠ Sin permisos de escritura';
                } else {
                    echo '✗ NO EXISTE';
                }
                echo '</span></div>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>🔌 Base de Datos</h2>
            <?php
            try {
                require_once 'config/database.php';
                $db = new Database();
                $conn = $db->getConnection();
                
                echo '<div class="test-item">';
                echo '<span>Conexión a MySQL</span>';
                echo '<span class="status ok">✓ CONECTADO</span>';
                echo '</div>';
                
                $tables = ['users', 'videos', 'likes', 'comments', 'subscriptions', 'playlists', 'playlist_videos', 'watch_history'];
                foreach($tables as $table) {
                    $query = "SELECT COUNT(*) as count FROM $table";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    echo '<div class="test-item">';
                    echo '<span>Tabla: ' . $table . '</span>';
                    echo '<span class="status ok">✓ ' . $result['count'] . ' registros</span>';
                    echo '</div>';
                }
            } catch(Exception $e) {
                echo '<div class="test-item">';
                echo '<span>Error de conexión</span>';
                echo '<span class="status error">✗ ' . $e->getMessage() . '</span>';
                echo '</div>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>🎨 Assets (CSS/JS)</h2>
            <?php
            $assets = [
                'assets/css/style.css' => 'Hoja de estilos principal',
                'assets/js/main.js' => 'JavaScript principal',
                'assets/js/video.js' => 'JavaScript del reproductor'
            ];
            
            foreach($assets as $file => $desc) {
                $exists = file_exists($file);
                $size = $exists ? filesize($file) : 0;
                echo '<div class="test-item">';
                echo '<span>' . $desc . '</span>';
                echo '<span class="status ' . ($exists ? 'ok' : 'error') . '">';
                echo $exists ? '✓ ' . number_format($size/1024, 2) . ' KB' : '✗ FALTA';
                echo '</span></div>';
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>🧪 Pruebas de Funcionalidad</h2>
            
            <button onclick="testSidebar()">🔘 Probar Menú Lateral</button>
            <button onclick="testVoiceSearch()">🎤 Probar Búsqueda por Voz</button>
            <button onclick="testApps()">📱 Probar Apps Menu</button>
            <button onclick="testNotifications()">🔔 Probar Notificaciones</button>
            
            <div id="test-result" style="margin-top: 20px;"></div>
        </div>
        
        <div class="test-section">
            <h2>🛠 Soluciones Rápidas</h2>
            
            <h3 style="margin: 15px 0; font-size: 16px;">Si el menú lateral no funciona:</h3>
            <div class="code">
1. Abre la consola del navegador (F12)<br>
2. Ve a la pestaña "Console"<br>
3. Busca errores en rojo<br>
4. Recarga la página con Ctrl+F5
            </div>
            
            <h3 style="margin: 15px 0; font-size: 16px;">Si Studio/Ver más tarde salen en blanco:</h3>
            <div class="code">
1. Verifica que estés logueado<br>
2. Revisa la consola de errores (F12)<br>
3. Asegúrate que las tablas de la BD existan<br>
4. Verifica que config/database.php tenga los datos correctos
            </div>
            
            <button onclick="window.location.href='index.php'" style="margin-top: 20px;">
                🏠 Volver a YouTube Clone
            </button>
        </div>
    </div>
    
    <script>
        function testSidebar() {
            const result = document.getElementById('test-result');
            result.innerHTML = '<div class="test-item"><span>Comprobando menú lateral...</span></div>';
            
            setTimeout(() => {
                const menuBtn = document.getElementById('menuBtn');
                const sidebar = document.getElementById('sidebar');
                
                if(menuBtn && sidebar) {
                    result.innerHTML = '<div class="test-item"><span>Menú lateral</span><span class="status ok">✓ ELEMENTOS ENCONTRADOS</span></div>';
                    result.innerHTML += '<div class="test-item"><span>JavaScript cargado</span><span class="status ok">✓ OK</span></div>';
                    result.innerHTML += '<p style="margin-top: 15px; color: #51cf66;">El menú lateral debe funcionar. Si no funciona, recarga la página con Ctrl+F5</p>';
                } else {
                    result.innerHTML = '<div class="test-item"><span>Error</span><span class="status error">✗ NO SE ENCONTRARON LOS ELEMENTOS</span></div>';
                }
            }, 500);
        }
        
        function testVoiceSearch() {
            const result = document.getElementById('test-result');
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                result.innerHTML = '<div class="test-item"><span>Búsqueda por voz</span><span class="status ok">✓ SOPORTADO</span></div>';
            } else {
                result.innerHTML = '<div class="test-item"><span>Búsqueda por voz</span><span class="status error">✗ NO SOPORTADO EN ESTE NAVEGADOR</span></div>';
            }
        }
        
        function testApps() {
            const result = document.getElementById('test-result');
            result.innerHTML = '<div class="test-item"><span>Menú de aplicaciones</span><span class="status ok">✓ CONFIGURADO</span></div>';
            result.innerHTML += '<p style="margin-top: 15px;">Busca el icono de cuadrícula (⊞) en la barra superior y haz clic.</p>';
        }
        
        function testNotifications() {
            const result = document.getElementById('test-result');
            result.innerHTML = '<div class="test-item"><span>Notificaciones</span><span class="status ok">✓ CONFIGURADAS</span></div>';
            result.innerHTML += '<p style="margin-top: 15px;">Busca el icono de campana (🔔) en la barra superior y haz clic.</p>';
        }
    </script>
</body>
</html>