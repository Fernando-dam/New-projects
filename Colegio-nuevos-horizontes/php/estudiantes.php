<?php
class Estudiantes {
    private $conn;
    private $table_name = "estudiantes";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Crear nuevo estudiante
    public function crear($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (nombre_completo, cedula, fecha_nacimiento, genero, grado, seccion, telefono, email, direccion, nombre_tutor, telefono_tutor, estado) 
                 VALUES 
                 (:nombre_completo, :cedula, :fecha_nacimiento, :genero, :grado, :seccion, :telefono, :email, :direccion, :nombre_tutor, :telefono_tutor, 'activo')";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $nombre_completo = htmlspecialchars(strip_tags($data['nombre_completo']));
        $cedula = htmlspecialchars(strip_tags($data['cedula']));
        $fecha_nacimiento = $data['fecha_nacimiento'];
        $genero = $data['genero'];
        $grado = $data['grado'];
        $seccion = htmlspecialchars(strip_tags($data['seccion']));
        $telefono = htmlspecialchars(strip_tags($data['telefono']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $direccion = htmlspecialchars(strip_tags($data['direccion']));
        $nombre_tutor = htmlspecialchars(strip_tags($data['nombre_tutor']));
        $telefono_tutor = htmlspecialchars(strip_tags($data['telefono_tutor']));
        
        // Vincular parámetros
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":fecha_nacimiento", $fecha_nacimiento);
        $stmt->bindParam(":genero", $genero);
        $stmt->bindParam(":grado", $grado);
        $stmt->bindParam(":seccion", $seccion);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":nombre_tutor", $nombre_tutor);
        $stmt->bindParam(":telefono_tutor", $telefono_tutor);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Listar todos los estudiantes
    public function listar() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY grado, seccion, nombre_completo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener estudiante por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Actualizar estudiante
    public function actualizar($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                 SET nombre_completo = :nombre_completo, cedula = :cedula, fecha_nacimiento = :fecha_nacimiento, 
                     genero = :genero, grado = :grado, seccion = :seccion, telefono = :telefono, 
                     email = :email, direccion = :direccion, nombre_tutor = :nombre_tutor, 
                     telefono_tutor = :telefono_tutor 
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $nombre_completo = htmlspecialchars(strip_tags($data['nombre_completo']));
        $cedula = htmlspecialchars(strip_tags($data['cedula']));
        $fecha_nacimiento = $data['fecha_nacimiento'];
        $genero = $data['genero'];
        $grado = $data['grado'];
        $seccion = htmlspecialchars(strip_tags($data['seccion']));
        $telefono = htmlspecialchars(strip_tags($data['telefono']));
        $email = htmlspecialchars(strip_tags($data['email']));
        $direccion = htmlspecialchars(strip_tags($data['direccion']));
        $nombre_tutor = htmlspecialchars(strip_tags($data['nombre_tutor']));
        $telefono_tutor = htmlspecialchars(strip_tags($data['telefono_tutor']));
        
        // Vincular parámetros
        $stmt->bindParam(":nombre_completo", $nombre_completo);
        $stmt->bindParam(":cedula", $cedula);
        $stmt->bindParam(":fecha_nacimiento", $fecha_nacimiento);
        $stmt->bindParam(":genero", $genero);
        $stmt->bindParam(":grado", $grado);
        $stmt->bindParam(":seccion", $seccion);
        $stmt->bindParam(":telefono", $telefono);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":direccion", $direccion);
        $stmt->bindParam(":nombre_tutor", $nombre_tutor);
        $stmt->bindParam(":telefono_tutor", $telefono_tutor);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Eliminar estudiante (cambiar estado)
    public function eliminar($id) {
        $query = "UPDATE " . $this->table_name . " SET estado = 'inactivo' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>