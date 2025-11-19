<?php
require_once '../modelos/conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../vista/nueva_mascota.php");
    exit;
}

$id_institucion = $_POST['id_institucion'] ?? '';
$nombre         = trim($_POST['nombre'] ?? '');
$tipo           = $_POST['tipo'] ?? '';
$raza           = trim($_POST['raza'] ?? '');
$edad           = trim($_POST['edad'] ?? '');
$tamano         = $_POST['tamano'] ?? '';
$descripcion    = trim($_POST['descripcion'] ?? '');
$estado         = $_POST['estado'] ?? 'Disponible';

$mensaje_error = null;

// Validación básica
if ($id_institucion === '' || $nombre === '' || $tipo === '' || $tamano === '') {
    $mensaje_error = "Faltan campos obligatorios.";
}

// Leer imagen (si se subió una)
$foto_binaria = null;

if (!$mensaje_error && isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        $mensaje_error = "Error al subir la imagen.";
    } else {
        $tmp_name = $_FILES['foto']['tmp_name'];
        $size     = $_FILES['foto']['size'];

        // límite opcional: 5 MB
        if ($size > 5 * 1024 * 1024) {
            $mensaje_error = "La imagen es demasiado grande (máximo 5MB).";
        } else {
            $foto_binaria = file_get_contents($tmp_name);
        }
    }
}

if ($mensaje_error) {
    $err = urlencode($mensaje_error);
    header("Location: ../vista/nueva_mascota.php?err=$err");
    exit;
}

try {
    $sql = "INSERT INTO mascota
            (id_institucion, nombre, tipo, raza, edad, tamano, descripcion, estado, foto)
            VALUES (:id_institucion, :nombre, :tipo, :raza, :edad, :tamano, :descripcion, :estado, :foto)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id_institucion', $id_institucion, PDO::PARAM_INT);
    $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
    $stmt->bindValue(':raza', $raza, PDO::PARAM_STR);
    $stmt->bindValue(':edad', $edad, PDO::PARAM_STR);
    $stmt->bindValue(':tamano', $tamano, PDO::PARAM_STR);
    $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
    $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);

    if ($foto_binaria !== null) {
        $stmt->bindParam(':foto', $foto_binaria, PDO::PARAM_LOB);
    } else {
        $stmt->bindValue(':foto', null, PDO::PARAM_NULL);
    }

    $stmt->execute();

    header("Location: ../vista/nueva_mascota.php?ok=1");
    exit;

} catch (PDOException $e) {
    $err = urlencode("Error al registrar la mascota: " . $e->getMessage());
    header("Location: ../vista/nueva_mascota.php?err=$err");
    exit;
}
