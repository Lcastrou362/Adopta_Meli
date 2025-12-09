<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !in_array($_SESSION['rol'], ['REFUGIO_ADM','ADMIN'])) {
    header("Location: index.php");
    exit();
}

require_once "../modelos/conexionBD.php";

// Construir filtro según rol
$where  = "";
$params = [];

if ($_SESSION['rol'] === 'REFUGIO_ADM') {
    $where = "WHERE f.id_institucion = :idInst";
    $params[':idInst'] = $_SESSION['id_institucion'];
}

// Puedes filtrar solo 'Enviado' si quieres:
// $where .= ($where ? " AND" : " WHERE") . " f.estado = 'Enviado'";

$sql = "SELECT 
            f.id_formulario,
            f.fecha_solicitud,
            f.estado,
            f.mensaje,
            f.telefono_usuario,
            f.correo_usuario,
            m.nombre  AS nombre_mascota,
            u.nombre  AS nombre_adoptante,
            i.nombre  AS nombre_institucion
        FROM formulario_adopcion f
        JOIN mascota m     ON f.id_mascota = m.id_mascota
        JOIN usuario u     ON f.id_usuario = u.id_usuario
        JOIN institucion i ON f.id_institucion = i.id_institucion
        $where
        ORDER BY f.fecha_solicitud DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de adopción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Solicitudes de adopción</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success mt-2">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($solicitudes)): ?>
        <div class="alert alert-info mt-3">
            No hay solicitudes de adopción registradas.
        </div>
    <?php else: ?>

    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Fecha</th>
                <th>Mascota</th>
                <th>Adoptante</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Mensaje</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($solicitudes as $s): ?>
            <tr>
                <td><?= $s['id_formulario'] ?></td>
                <td><?= $s['fecha_solicitud'] ?></td>
                <td><?= htmlspecialchars($s['nombre_mascota']) ?></td>
                <td><?= htmlspecialchars($s['nombre_adoptante']) ?></td>
                <td><?= htmlspecialchars($s['telefono_usuario']) ?></td>
                <td><?= htmlspecialchars($s['correo_usuario']) ?></td>
                <td><?= nl2br(htmlspecialchars($s['mensaje'])) ?></td>
                <td><?= $s['estado'] ?></td>
                <td>
                    <?php if ($s['estado'] === 'Enviado'): ?>
                        <form action="../controladores/responder_solicitud.php" method="post" class="d-inline">
                            <input type="hidden" name="id_formulario" value="<?= $s['id_formulario'] ?>">
                            <button type="submit" name="accion" value="aprobar" class="btn btn-success btn-sm">
                                Aprobar
                            </button>
                        </form>

                        <form action="../controladores/responder_solicitud.php" method="post" class="d-inline">
                            <input type="hidden" name="id_formulario" value="<?= $s['id_formulario'] ?>">
                            <button type="submit" name="accion" value="rechazar" class="btn btn-danger btn-sm">
                                Rechazar
                            </button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">Sin acciones</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php endif; ?>
</div>

</body>
</html>
