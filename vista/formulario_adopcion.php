<?php
session_start();

// Validación: SOLO usuarios registrados pueden adoptar
if (!isset($_SESSION['id_usuario'])) {
    echo "<script>
            alert('Debes iniciar sesión para solicitar una adopción.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

require_once '../modelos/conexionBD.php';

if (!isset($_GET['id_mascota'])) {
    die('Mascota no especificada.');
}

$id_mascota = (int) $_GET['id_mascota'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de adopción</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <h2>Formulario de adopción</h2>
    <p>Completa tus datos y el motivo por el que quieres adoptar esta mascota.</p>

    <form method="post" action="../controladores/registrar_adopcion.php">
        <!-- Estos van ocultos, se usan en el controlador -->
        <input type="hidden" name="id_mascota" value="<?php echo $id_mascota; ?>">
        <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>">

        <div class="mb-3">
            <label class="form-label">Mensaje para el refugio</label>
            <textarea name="mensaje" class="form-control" rows="4"
                      placeholder="Cuenta brevemente por qué quieres adoptar esta mascota"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

</body>
</html>
