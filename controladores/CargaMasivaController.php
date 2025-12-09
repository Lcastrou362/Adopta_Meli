<?php
require_once "../modelos/conexionBD.php";

$csvPath = "../cargas_masivas/mascotas.csv";
$fotosPath = "../cargas_masivas/fotos/";

if (!file_exists($csvPath)) {
    die("ERROR: No se encontró el archivo mascotas.csv");
}

$archivo = fopen($csvPath, "r");
$encabezado = fgetcsv($archivo); // Ignorar encabezado

$contador = 0;

while (($datos = fgetcsv($archivo)) !== false) {

    list($nombre, $tipo, $raza, $edad, $tamano, $descripcion, $fotoNombre, $idInstitucion) = $datos;

    $rutaFoto = $fotosPath . $fotoNombre;

    if (!file_exists($rutaFoto)) {
        echo "Foto NO encontrada: $rutaFoto<br>";
        continue;
    }

    $foto = file_get_contents($rutaFoto);

    $sql = "INSERT INTO mascota 
            (id_institucion, nombre, tipo, raza, edad, tamano, descripcion, foto, estado)
            VALUES
            (:idIns, :nombre, :tipo, :raza, :edad, :tamano, :descripcion, :foto, 'Disponible')";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(":idIns", $idInstitucion);
    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":tipo", $tipo);
    $stmt->bindParam(":raza", $raza);
    $stmt->bindParam(":edad", $edad);
    $stmt->bindParam(":tamano", $tamano);
    $stmt->bindParam(":descripcion", $descripcion);
    $stmt->bindParam(":foto", $foto, PDO::PARAM_LOB);

    $stmt->execute();

    echo "Mascota cargada: $nombre <br>";
    $contador++;
}

fclose($archivo);

echo "<hr>";
echo "<strong>Carga masiva completada.</strong><br>";
echo "Total de mascotas cargadas: $contador";
