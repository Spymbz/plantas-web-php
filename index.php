<?php
$serverName = "server-bd-plantas.database.windows.net,1433";
$database = "bd-plantas-nativas";
$username = "rootbd";
$password = "Benjamin123.";

try {
    $db = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla sucursales si no existe
    $db->exec("IF OBJECT_ID('sucursales', 'U') IS NULL
               CREATE TABLE sucursales (
                   id INT IDENTITY(1,1) PRIMARY KEY,
                   nombre NVARCHAR(255),
                   direccion NVARCHAR(255),
                   ciudad NVARCHAR(255)
               )");

    // Insertar sucursal si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        if ($nombre && $direccion && $ciudad) {
            $stmt = $db->prepare("INSERT INTO sucursales (nombre, direccion, ciudad) VALUES (?, ?, ?)");
            $stmt->execute([$nombre, $direccion, $ciudad]);
        }
    }

    // Obtener todas las sucursales
    $sucursales = $db->query("SELECT * FROM sucursales ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestión de Sucursales - Plantas Nativas de Chile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1, h2 { color: #2C6E49; }
        form { margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        label { display: block; margin-bottom: 10px; }
        input[type="text"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { background-color: #2C6E49; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #1A4D2E; }
        ul { list-style-type: none; padding: 0; }
        li { background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Gestión de Sucursales</h1>
    <form method="POST">
        <label>Nombre de Sucursal: <input type="text" name="nombre" required></label>
        <label>Dirección: <input type="text" name="direccion" required></label>
        <label>Ciudad: <input type="text" name="ciudad" required></label>
        <button type="submit">Agregar Sucursal</button>
    </form>

    <h2>Lista de Sucursales</h2>
    <ul>
        <?php foreach ($sucursales as $sucursal): ?>
            <li><strong><?= htmlspecialchars($sucursal['nombre']) ?></strong> - <?= htmlspecialchars($sucursal['direccion']) ?> (<?= htmlspecialchars($sucursal['ciudad']) ?>)</li>
        <?php endphp; ?>
    </ul>
</body>
</html>