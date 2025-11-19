<?php
require_once '../modelos/conexionBD.php';

// Obtener instituciones para el SELECT
$stmt = $pdo->prepare("SELECT id_institucion, nombre FROM institucion WHERE estado = 'Activa'");
$stmt->execute();
$instituciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensaje_exito = isset($_GET['ok']) ? "Mascota registrada exitosamente." : null;
$mensaje_error = isset($_GET['err']) ? $_GET['err'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Mascota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Fondo con imagen local: OJO, assets está DENTRO de vista */
        body {
            background-image: url('assets/img/fondo_mascotas.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        /* Capa encima del fondo para que no se vea tan cargado */
        .overlay {
            min-height: 100vh;
            background: rgba(255, 255, 255, 0.60);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 10px;
        }

        .card {
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.96);
            border-radius: 20px;
        }

        .card-header {
            border-top-left-radius: 20px !important;
            border-top-right-radius: 20px !important;
            background: linear-gradient(90deg, #ff9a9e, #fad0c4);
        }

        .titulo-principal {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .titulo-principal .emojis {
            font-size: 1.6rem;
        }
    </style>
</head>
<body>

<div class="overlay">
    <div class="container" style="max-width: 900px;">
        <div class="card shadow-lg">
            <div class="card-header text-white">
                <div class="titulo-principal">
                    <span class="emojis">🐶🐱</span>
                    <h4 class="mb-0">Registrar Mascota para Adopción</h4>
                </div>
            </div>

            <div class="card-body">

                <?php if ($mensaje_exito): ?>
                    <div class="alert alert-success mb-3">
                        <?php echo htmlspecialchars($mensaje_exito); ?>
                    </div>
                <?php endif; ?>

                <?php if ($mensaje_error): ?>
                    <div class="alert alert-danger mb-3">
                        <?php echo htmlspecialchars($mensaje_error); ?></div>
                <?php endif; ?>

                <p class="text-muted mb-4">
                    Completa los datos de la mascota que será publicada para adopción responsable.
                </p>

                <form action="../controladores/registrar_mascota.php"
                      method="POST"
                      enctype="multipart/form-data"
                      class="row g-3">

                    <!-- INSTITUCIÓN -->
                    <div class="col-md-6">
                        <label class="form-label">Institución *</label>
                        <select name="id_institucion" class="form-select" required>
                            <option value="">Seleccione una institución...</option>
                            <?php foreach ($instituciones as $inst): ?>
                                <option value="<?php echo $inst['id_institucion']; ?>">
                                    <?php echo htmlspecialchars($inst['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- NOMBRE -->
                    <div class="col-md-6">
                        <label class="form-label">Nombre de la mascota *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>

                    <!-- ESPECIE -->
                    <div class="col-md-4">
                        <label class="form-label">Especie *</label>
                        <select name="tipo" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <!-- RAZA -->
                    <div class="col-md-4">
                        <label class="form-label">Raza</label>
                        <input type="text" name="raza" class="form-control">
                    </div>

                    <!-- EDAD -->
                    <div class="col-md-4">
                        <label class="form-label">Edad</label>
                        <input type="text" name="edad" class="form-control" placeholder="Ej: 3 meses, 2 años">
                    </div>

                    <!-- TAMAÑO -->
                    <div class="col-md-6">
                        <label class="form-label">Tamaño *</label>
                        <select name="tamano" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <option value="Pequeño">Pequeño</option>
                            <option value="Mediano">Mediano</option>
                            <option value="Grande">Grande</option>
                        </select>
                    </div>

                    <!-- ESTADO -->
                    <div class="col-md-6">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="Disponible">Disponible</option>
                            <option value="Adoptado">Adoptado</option>
                        </select>
                    </div>

                    <!-- FOTO -->
                    <div class="col-md-6">
                        <label class="form-label">Foto de la mascota</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">La imagen se guardará en la base de datos.</small>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div class="col-md-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"
                                  placeholder="Cuenta un poco sobre su carácter, si se lleva bien con niños, otros animales, etc."></textarea>
                    </div>

                    <!-- BOTONES -->
                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            🐾 Registrar Mascota
                        </button>
                        <a href="index.php" class="btn btn-outline-secondary">
                            Volver
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
