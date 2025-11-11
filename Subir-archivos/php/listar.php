<?php
header('Content-Type: application/json');

$directorio = '../archivos';
$archivos = array();

// Verificar si el directorio existe
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// Abrir el directorio usando opendir()
if ($dir = opendir($directorio)) {
    
    // Leer cada entrada del directorio usando readdir()
    while (($archivo = readdir($dir)) !== false) {
        
        // Ignorar . y ..
        if ($archivo != "." && $archivo != "..") {
            
            $rutaCompleta = $directorio . '/' . $archivo;
            
            // Verificar que sea un archivo (no un directorio)
            if (is_file($rutaCompleta)) {
                
                // Obtener información del archivo
                $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
                $tamano = filesize($rutaCompleta);
                $fecha = date("d/m/Y H:i", filemtime($rutaCompleta));
                
                // Agregar al array de archivos
                $archivos[] = array(
                    'nombre' => $archivo,
                    'extension' => $extension,
                    'tamano' => formatearTamano($tamano),
                    'tamanoBytes' => $tamano,
                    'fecha' => $fecha,
                    'timestamp' => filemtime($rutaCompleta)
                );
            }
        }
    }
    
    // Cerrar el directorio
    closedir($dir);
    
    // Ordenar archivos por fecha (más recientes primero)
    usort($archivos, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
    
    echo json_encode([
        'success' => true,
        'archivos' => $archivos,
        'total' => count($archivos)
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se pudo abrir el directorio de archivos.',
        'archivos' => []
    ]);
}

// Función para formatear el tamaño del archivo
function formatearTamano($bytes) {
    $unidades = array('B', 'KB', 'MB', 'GB');
    $i = 0;
    
    while ($bytes >= 1024 && $i < count($unidades) - 1) {
        $bytes /= 1024;
        $i++;
    }
    
    return round($bytes, 2) . ' ' . $unidades[$i];
}
?>