<?php
require_once '../modelos/conexionBD.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID inválido");
}

$stmt = $pdo->prepare("SELECT foto FROM mascota WHERE id_mascota = ?");
$stmt->execute([$id]);
$mascota = $stmt->fetch(PDO::FETCH_ASSOC);

if ($mascota && !empty($mascota['foto'])) {
    header("Content-Type: image/jpeg");
    echo $mascota['foto'];
} else {
    die("No hay foto disponible");
}
