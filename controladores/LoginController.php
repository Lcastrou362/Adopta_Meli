<?php
session_start();
require_once "../modelos/conexionBD.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vista/login.php");
    exit();
}

$correo   = trim($_POST["correo"] ?? "");
$password = $_POST["password"] ?? "";

// Validación básica
if ($correo === "" || $password === "") {
    header("Location: ../vista/login.php?error=Debes ingresar correo y contraseña");
    exit();
}

// Buscar usuario por correo
$sql = "SELECT id_usuario, rut, id_institucion, nombre, correo, password_hash, telefono, rol
        FROM usuario
        WHERE correo = :correo
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([':correo' => $correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: ../vista/login.php?error=Usuario o contraseña incorrectos");
    exit();
}

// Validar contraseña
if (!password_verify($password, $usuario["password_hash"])) {
    header("Location: ../vista/login.php?error=Usuario o contraseña incorrectos");
    exit();
}

// LOGIN CORRECTO → guardar sesión
$_SESSION["id_usuario"]    = $usuario["id_usuario"];
$_SESSION["usuario"]       = $usuario["nombre"];
$_SESSION["rol"]           = $usuario["rol"];
$_SESSION["id_institucion"] = $usuario["id_institucion"]; // puede ser NULL para adoptantes

// Redirigir al index
header("Location: ../vista/index.php");
exit();