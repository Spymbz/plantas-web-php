<?php
// Conexión a base de datos SQLite en memoria
$db = new PDO('sqlite::memory:');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Crear tabla plantas si no existe
$db->exec("CREATE TABLE IF NOT EXISTS plantas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre TEXT,
    tipo TEXT
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
