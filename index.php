<?php
$serverName = "server-bd-plantas.database.windows.net";
$database = "bd-plantas-nativas";
$username = "rootbd";
$password = "Benjamin123.";

try {
    // Conexión a Azure SQL
    $db = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla plantas si no existe
    $checkTable = $db->query("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'plantas'");
    if ($checkTable->rowCount() == 0) {
        $db->exec("
            CREATE TABLE plantas (
                id INT IDENTITY(1,1) PRIMARY KEY,
                nombre NVARCHAR(255) NOT NULL,
                tipo NVARCHAR(255) NOT NULL
            )
        ");
    }

    // Insertar planta si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre'] ?? '');
        $tipo   = trim($_POST['tipo'] ?? '');

        if ($nombre && $tipo) {
            $stmt = $db->prepare("INSERT INTO plantas (nombre, tipo) VALUES (?, ?)");
            $stmt->execute([$nombre, $tipo]);
        }
    }

    // Obtener todas las plantas ordenadas por nombre
    $plantas = $db->query("SELECT * FROM plantas ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plantas Nativas de Chile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        h1, h2 { color: #2C6E49; }
        form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        label { display: block; margin-bottom: 10px; }
        input[type="text"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { background-color: #2C6E49; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #1A4D2E; }
        ul { list-style: none; padding: 0; }
        li { background-color: #fff; margin-bottom: 8px; padding: 10px; border-radius: 5px; border-left: 5px solid #2C6E49; }
    </style>
</head>
<body>
    <h1>Plantas Nativas de Chile</h1>

    <form method="POST">
        <h2>Agregar Nueva Planta</h2>
        <label>Nombre:
            <input type="text" name="nombre" required>
        </label>
        <label>Tipo:
            <input type="text" name="tipo" required>
        </label>
        <button type="submit">Guardar Planta</button>
    </form>

    <h2>Lista de Plantas</h2>
    <?php if (count($plantas) > 0): ?>
        <ul>
            <?php foreach ($plantas as $planta): ?>
                <li><strong><?= htmlspecialchars($planta['nombre']) ?></strong> (<?= htmlspecialchars($planta['tipo']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay plantas registradas.</p>
    <?php endif; ?>
</body>
</html>
