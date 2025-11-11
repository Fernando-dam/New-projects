<?php
header('Content-Type: application/json');

// Configuración
$formatos = array('.jpg', '.jpeg', '.png', '.pdf');
$directorio = '../archivos';
$maxSize = 5 * 1024 * 1024; // 5MB

// Crear directorio si no existe
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

// CORREGIDO: Verificar si se recibió el archivo
if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] !== UPLOAD_ERR_NO_FILE) {
    
    // Obtener información del archivo
    $nombreArchivo = $_FILES['archivo']['name'];
    $nombreTmpArchivo = $_FILES['archivo']['tmp_name'];
    $tamanoArchivo = $_FILES['archivo']['size'];
    $errorArchivo = $_FILES['archivo']['error'];
    
    // Validar que no haya errores en la subida
    if ($errorArchivo !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al subir el archivo. Código de error: ' . $errorArchivo
        ]);
        exit;
    }
    
    // Validar tamaño del archivo
    if ($tamanoArchivo > $maxSize) {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo es demasiado grande. Máximo 5MB permitido.'
        ]);
        exit;
    }
    
    // Obtener la extensión del archivo
    $extension = strtolower(strrchr($nombreArchivo, '.'));
    
    // Verificar si el formato es permitido usando in_array()
    if (in_array($extension, $formatos)) {
        
        // Generar un nombre único para evitar sobrescrituras
        $nombreUnico = pathinfo($nombreArchivo, PATHINFO_FILENAME) . '_' . time() . $extension;
        $rutaDestino = $directorio . '/' . $nombreUnico;
        
        // Mover el archivo a la carpeta de destino usando move_uploaded_file()
        if (move_uploaded_file($nombreTmpArchivo, $rutaDestino)) {
            
            echo json_encode([
                'success' => true,
                'message' => 'Archivo subido exitosamente: ' . $nombreUnico,
                'archivo' => $nombreUnico
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al mover el archivo a la carpeta de destino.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de archivo no permitido. Solo se aceptan: ' . implode(', ', $formatos)
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se ha enviado ningún archivo.'
    ]);
}
?>