<?php
session_start();
require_once "../modelos/conexionBD.php";

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['REFUGIO_ADM','ADMIN'])) {
    header("Location: ../vista/index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../vista/solicitudes_pendientes.php");
    exit();
}

$idFormulario = isset($_POST['id_formulario']) ? (int) $_POST['id_formulario'] : 0;
$accion       = $_POST['accion'] ?? '';

if ($idFormulario <= 0 || !in_array($accion, ['aprobar','rechazar'])) {
    header("Location: ../vista/solicitudes_pendientes.php?msg=" . urlencode("Solicitud inválida"));
    exit();
}

// Obtener info de la solicitud
$sql = "SELECT id_mascota, id_institucion, estado 
        FROM formulario_adopcion
        WHERE id_formulario = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $idFormulario]);
$form = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$form) {
    header("Location: ../vista/solicitudes_pendientes.php?msg=" . urlencode("La solicitud no existe"));
    exit();
}

// Validar que el refugio solo pueda manejar sus solicitudes
if ($_SESSION['rol'] === 'REFUGIO_ADM' && $_SESSION['id_institucion'] != $form['id_institucion']) {
    header("Location: ../vista/solicitudes_pendientes.php?msg=" . urlencode("No tienes permiso para gestionar esta solicitud"));
    exit();
}

$idMascota      = $form['id_mascota'];
$idInstitucion  = $form['id_institucion'];

try {
    $pdo->beginTransaction();

    if ($accion === 'aprobar') {
        // Aprobar → solicitud aprobada y mascota adoptada
        $stmt1 = $pdo->prepare("UPDATE formulario_adopcion 
                                SET estado = 'Aprobado'
                                WHERE id_formulario = :id");
        $stmt1->execute([':id' => $idFormulario]);

        $stmt2 = $pdo->prepare("UPDATE mascota 
                                SET estado = 'Adoptada'
                                WHERE id_mascota = :m");
        $stmt2->execute([':m' => $idMascota]);

        $msg = "Solicitud aprobada y mascota marcada como adoptada.";
    } else {
        // Rechazar → solicitud rechazada y mascota queda disponible
        $stmt1 = $pdo->prepare("UPDATE formulario_adopcion 
                                SET estado = 'Rechazado'
                                WHERE id_formulario = :id");
        $stmt1->execute([':id' => $idFormulario]);

        $stmt2 = $pdo->prepare("UPDATE mascota 
                                SET estado = 'Disponible'
                                WHERE id_mascota = :m");
        $stmt2->execute([':m' => $idMascota]);

        $msg = "Solicitud rechazada. La mascota sigue disponible.";
    }

    $pdo->commit();

    header("Location: ../vista/solicitudes_pendientes.php?msg=" . urlencode($msg));
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al responder solicitud: " . $e->getMessage());
}
