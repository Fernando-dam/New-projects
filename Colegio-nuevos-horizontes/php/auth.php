<?php
session_start();
require_once 'database.php';

class Auth {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function login($usuario, $password, $rol) {
        try {
            $query = "SELECT * FROM usuarios WHERE usuario = :usuario AND rol = :rol AND estado = 'activo'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->bindParam(":rol", $rol);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // En un sistema real, usar password_verify()
                if ($password === $row['password']) {
                    $_SESSION['usuario_id'] = $row['id'];
                    $_SESSION['usuario'] = $row['usuario'];
                    $_SESSION['rol'] = $row['rol'];
                    $_SESSION['nombre'] = $row['nombre'];
                    
                    return true;
                }
            }
            return false;
        } catch(PDOException $exception) {
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        header("Location: ../index.php");
        exit();
    }
    
    public function checkAuth() {
        return isset($_SESSION['usuario_id']);
    }
    
    public function checkRole($allowedRoles) {
        if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $allowedRoles)) {
            header("Location: ../unauthorized.php");
            exit();
        }
    }
}

// Procesar login
if ($_POST && isset($_POST['usuario']) && isset($_POST['password']) && isset($_POST['rol'])) {
    $auth = new Auth($db);
    
    if (empty($_POST['usuario']) || empty($_POST['password']) || empty($_POST['rol'])) {
        header("Location: ../index.php?error=vacio");
        exit();
    }
    
    if ($auth->login($_POST['usuario'], $_POST['password'], $_POST['rol'])) {
        header("Location: ../dashboard.php");
        exit();
    } else {
        header("Location: ../index.php?error=credenciales");
        exit();
    }
}

// Procesar logout
if (isset($_GET['logout'])) {
    $auth = new Auth($db);
    $auth->logout();
}
?>