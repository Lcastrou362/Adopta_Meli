<?php
session_start();
require_once "../modelos/conexionBD.php";

function validarRut($rut) {
    $rut = preg_replace('/[\.\-]/', '', strtoupper(trim($rut)));
    if (strlen($rut) < 8) return false;

    $dv  = substr($rut, -1);
    $num = substr($rut, 0, -1);

    if (!ctype_digit($num)) return false;

    $suma = 0;
    $multiplo = 2;

    for ($i = strlen($num) - 1; $i >= 0; $i--) {
        $suma += $multiplo * intval($num[$i]);
        $multiplo = ($multiplo < 7) ? $multiplo + 1 : 2;
    }

    $resto = $suma % 11;
    $dv_calc = 11 - $resto;

    if ($dv_calc == 11) $dv_calc = '0';
    elseif ($dv_calc == 10) $dv_calc = 'K';

    return strtoupper($dv) === strtoupper($dv_calc);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vista/registro_usuario.php");
    exit();
}

$rut      = trim($_POST["rut"]);
$nombre   = trim($_POST["nombre"]);
$correo   = trim($_POST["correo"]);
$telefono = trim($_POST["telefono"]);
$pass1    = $_POST["password"];
$pass2    = $_POST["password2"];

// Validaciones
if (!validarRut($rut)) {
    header("Location: ../vista/registro_usuario.php?error=RUT inválido");
    exit();
}

if ($pass1 !== $pass2) {
    header("Location: ../vista/registro_usuario.php?error=Las contraseñas no coinciden");
    exit();
}

// Validar correo único
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
$stmt->execute([$correo]);
if ($stmt->fetch()) {
    header("Location: ../vista/registro_usuario.php?error=Correo ya registrado");
    exit();
}

// Validar RUT único
$stmt = $pdo->prepare("SELECT id_usuario FROM usuario WHERE rut = ?");
$stmt->execute([$rut]);
if ($stmt->fetch()) {
    header("Location: ../vista/registro_usuario.php?error=RUT ya registrado");
    exit();
}

// Insertar usuario nuevo
$hash = password_hash($pass1, PASSWORD_DEFAULT);

// Rol SIEMPRE = ADOPTANTE
$sql = "INSERT INTO usuario (rut, nombre, correo, telefono, password_hash, rol)
        VALUES (:rut, :nombre, :correo, :telefono, :password_hash, 'ADOPTANTE')";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':rut'           => $rut,
    ':nombre'        => $nombre,
    ':correo'        => $correo,
    ':telefono'      => $telefono,
    ':password_hash' => $hash
]);

// Login automático
$_SESSION["id_usuario"] = $pdo->lastInsertId();
$_SESSION["usuario"]    = $nombre;
$_SESSION["rol"]        = 'ADOPTANTE';

header("Location: ../vista/index.php?registro=ok");
exit();
