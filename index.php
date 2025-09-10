<?php
$serverName = "newDatabaseNewServer.database.windows.net"; // tu servidor
$database = "bd-plantas-nativas";
$username = "rootbd";
$password = "TU_PASSWORD_AQUI";

try {
    $db = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla plantas si no existe
    $db->exec("IF OBJECT_ID('plantas', 'U') IS NULL
               CREATE TABLE plantas (
                   id INT IDENTITY(1,1) PRIMARY KEY,
                   nombre NVARCHAR(255),
                   tipo NVARCHAR(255)
               )");

    // Insertar planta si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        if ($nombre && $tipo) {
            $stmt = $db->prepare("INSERT INTO plantas (nombre, tipo) VALUES (?, ?)");
            $stmt->execute([$nombre, $tipo]);
        }
    }

    // Obtener todas las plantas
    $plantas = $db->query("SELECT * FROM plantas")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Plantas</title>
</head>
<body>
    <h1>Agregar Planta</h1>
    <form method="POST">
        <label>Nombre: <input type="text" name="nombre" required></label><br>
        <label>Tipo: <input type="text" name="tipo" required></label><br>
        <button type="submit">Guardar</button>
    </form>

    <h2>Lista de Plantas</h2>
    <ul>
        <?php foreach ($plantas as $planta): ?>
            <li><?= htmlspecialchars($planta['nombre']) ?> (<?= htmlspecialchars($planta['tipo']) ?>)</li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
