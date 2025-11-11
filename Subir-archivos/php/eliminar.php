<?php
header('Content-Type: application/json');

$directorio = '../archivos';

// Verificar si se envió el nombre del archivo
if (isset($_POST['archivo'])) {
    
    $nombreArchivo = $_POST['archivo'];
    $rutaArchivo = $directorio . '/' . $nombreArchivo;
    
    // Validar que el archivo exista
    if (file_exists($rutaArchivo)) {
        
        // Eliminar el archivo
        if (unlink($rutaArchivo)) {
            
            echo json_encode([
                'success' => true,
                'message' => 'Archivo eliminado exitosamente: ' . $nombreArchivo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar el archivo.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo no existe.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se especificó el archivo a eliminar.'
    ]);
}
?>