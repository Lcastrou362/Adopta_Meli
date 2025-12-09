<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4" style="max-width: 500px;">
    <h3 class="text-center">Crear cuenta para adoptar</h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <form action="../controladores/registrar_usuario.php" method="post">

        <div class="mb-3">
            <label class="form-label">RUT *</label>
            <input type="text" name="rut" class="form-control" required placeholder="12345678-9">
        </div>

        <div class="mb-3">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo *</label>
            <input type="email" name="correo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono *</label>
            <input type="text" name="telefono" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña *</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Repetir contraseña *</label>
            <input type="password" name="password2" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Registrarme</button>
        <a href="index.php" class="btn btn-secondary w-100 mt-2">Volver</a>
    </form>
</div>

</body>
</html>
