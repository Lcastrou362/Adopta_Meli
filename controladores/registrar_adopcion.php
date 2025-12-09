<?php
session_start();
require_once "../modelos/conexionBD.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vista/index.php");
    exit();
}

$idMascota = isset($_POST["id_mascota"]) ? (int) $_POST["id_mascota"] : 0;
$idUsuario = isset($_POST["id_usuario"]) ? (int) $_POST["id_usuario"] : 0;
$mensaje   = trim($_POST["mensaje"] ?? "");

if ($idMascota <= 0 || $idUsuario <= 0 || $mensaje === "") {
    die("Error: Datos inválidos.");
}

// 1) Obtener la institución de la mascota
$sqlInst = $pdo->prepare("SELECT id_institucion FROM mascota WHERE id_mascota = :id");
$sqlInst->execute([":id" => $idMascota]);
$idInstitucion = $sqlInst->fetchColumn();

if (!$idInstitucion) {
    die("Error: La mascota no existe.");
}

// 2) Obtener datos del usuario (teléfono y correo)
$sqlUser = $pdo->prepare("SELECT telefono, correo FROM usuario WHERE id_usuario = :id");
$sqlUser->execute([":id" => $idUsuario]);
$usuarioDatos = $sqlUser->fetch(PDO::FETCH_ASSOC);

if (!$usuarioDatos) {
    die("Error: El usuario no existe.");
}

$telefonoUsuario = $usuarioDatos["telefono"] ?? null;
$correoUsuario   = $usuarioDatos["correo"] ?? null;

try {
    $pdo->beginTransaction();

    // 3) Insertar en formulario_adopcion
    //    estado se deja 'Enviado' (por default) o lo puedes fijar explícitamente.
    $sql = "INSERT INTO formulario_adopcion 
            (id_mascota, id_usuario, telefono_usuario, correo_usuario, mensaje, estado, id_institucion, fecha_solicitud)
            VALUES
            (:id_mascota, :id_usuario, :tel, :correo, :mensaje, 'Enviado', :id_institucion, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id_mascota"     => $idMascota,
        ":id_usuario"     => $idUsuario,
        ":tel"            => $telefonoUsuario,
        ":correo"         => $correoUsuario,
        ":mensaje"        => $mensaje,
        ":id_institucion" => $idInstitucion
    ]);

    // 4) NO cambiamos el estado de la mascota aquí.
    //    La mascota sigue 'Disponible' hasta que el refugio apruebe la adopción.

    $pdo->commit();

    header("Location: ../vista/index.php?solicitud=ok");
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("ERROR en registrar adopción: " . $e->getMessage());
}
