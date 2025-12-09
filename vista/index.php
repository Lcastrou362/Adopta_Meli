<?php
session_start();
require_once '../modelos/conexionBD.php';

// Obtener todas las mascotas disponibles
$sql = "SELECT 
            m.id_mascota,
            m.nombre,
            m.tipo,
            m.raza,
            m.edad,
            m.tamano,
            m.descripcion,
            m.foto,
            i.nombre AS nombre_institucion
        FROM mascota m
        INNER JOIN institucion i ON m.id_institucion = i.id_institucion
        WHERE m.estado = 'Disponible'
        ORDER BY m.id_mascota DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AdoptaMeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-image: url('assets/img/fondo_mascotas.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .overlay {
            background: rgba(255, 255, 255, 0.65);
            min-height: 100vh;
            padding: 20px 0;
        }

        .card-img-top {
            width: 100%;
            height: 230px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            backdrop-filter: blur(4px);
        }

        .titulo {
            text-align: center;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 25px;
            font-size: 2rem;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">

    <a class="navbar-brand" href="index.php">AdoptaMeli</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <ul class="navbar-nav ms-auto">

        <!-- Inicio siempre visible -->
        <li class="nav-item">
          <a class="nav-link" href="index.php">Inicio</a>
        </li>

        <?php if (!isset($_SESSION['id_usuario'])): ?>
            <!-- Usuario NO logueado -->
            <li class="nav-item">
              <a class="nav-link" href="registro_usuario.php">Registrarse</a>
            </li>

            <li class="nav-item">
              <a class="nav-link" href="login.php">Iniciar Sesión</a>
            </li>

        <?php else: ?>

            <!-- Mostrar saludo -->
            <li class="nav-item">
              <span class="navbar-text me-2 text-white">
                Hola, <?php echo htmlspecialchars($_SESSION['usuario']); ?>
              </span>
            </li>

            <!-- ADMIN o REFUGIO_ADM -->
            <?php if ($_SESSION['rol'] === 'ADMIN' || $_SESSION['rol'] === 'REFUGIO_ADM'): ?>
                <li class="nav-item">
                  <a class="nav-link" href="nueva_mascota.php">Registrar Mascota</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="solicitudes_pendientes.php">Solicitudes</a>
                </li>
            <?php endif; ?>

            <!-- Cerrar sesión -->
            <li class="nav-item">
              <a class="nav-link" href="../controladores/LogoutController.php">Cerrar Sesión</a>
            </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- CONTENIDO -->
<div class="overlay">
    <div class="container mt-4">
        <h1 class="titulo">🐶🐱 Mascotas Disponibles para Adopción</h1>

        <div class="row g-4">

            <?php if (count($mascotas) === 0): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">No hay mascotas disponibles en este momento.</div>
                </div>
            <?php endif; ?>

            <?php foreach ($mascotas as $m): ?>
                <div class="col-md-4">
                    <div class="card">

                        <!-- Mostrar imagen -->
                        <?php if (!empty($m['foto'])): ?>
                            <img src="mostrar_foto.php?id=<?php echo $m['id_mascota']; ?>" class="card-img-top">
                        <?php else: ?>
                            <img src="assets/img/sin_foto.png" class="card-img-top">
                        <?php endif; ?>

                        <div class="card-body">
                            <h5 class="card-title text-center">
                                <?php echo htmlspecialchars($m['nombre']); ?>
                            </h5>

                            <p class="card-text">
                                <strong>Especie:</strong> <?php echo $m['tipo']; ?><br>
                                <strong>Raza:</strong> <?php echo $m['raza'] ?: 'No registrada'; ?><br>
                                <strong>Edad:</strong> <?php echo $m['edad'] ?: 'Desconocida'; ?><br>
                                <strong>Tamaño:</strong> <?php echo $m['tamano']; ?><br>
                                <strong>Refugio:</strong> <?php echo $m['nombre_institucion']; ?>
                            </p>

                            <!-- BOTÓN SEGÚN ROL -->
                            <?php
                            if (!isset($_SESSION['id_usuario'])) {
                                // Sin sesión → debe iniciar sesión
                            ?>
                                <a href="login.php" class="btn btn-primary w-100">
                                    Inicia sesión para adoptar
                                </a>
                            <?php
                            } else {

                                if ($_SESSION['rol'] === 'ADOPTANTE' || $_SESSION['rol'] === 'ADMIN') {
                            ?>
                                    <a href="formulario_adopcion.php?id_mascota=<?php echo $m['id_mascota']; ?>" 
                                       class="btn btn-success w-100">
                                       Solicitar adopción
                                    </a>
                            <?php
                                }
                                else if ($_SESSION['rol'] === 'REFUGIO_ADM') {
                            ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        Solo adoptantes pueden adoptar
                                    </button>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
