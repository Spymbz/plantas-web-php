<?php
// ¡¡IMPORTANTE!! Asegúrate de que estas credenciales coincidan EXACTAMENTE con tu Azure SQL Database
$serverName = "servidor-plantas-nativas-rb.database.windows.net,1433"; // Reemplaza con el nombre de tu servidor SQL de Azure
$database = "bd-plantas-nativas";                                  // Tu nombre de base de datos
$username = "rootbd";                                              // Tu usuario administrador de la base de datos
$password = "Benjamin123.";                                        // Tu contraseña de administrador de la base de datos

// Asegúrate de que el puerto 1433 esté incluido si no es el predeterminado.
// La forma correcta de la cadena de conexión para SQL Server en PHP es:
// dsn: "sqlsrv:Server=tu_servidor.database.windows.net,1433;Database=tu_base_de_datos"

try {
    // Conexión a la base de datos usando PDO y el driver SQLSRV
    $db = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Para que lance excepciones en caso de error

    // 1. Crear tabla 'plantas' si no existe
    // Esto es crucial para que funcione la primera vez.
    $db->exec("IF OBJECT_ID('plantas', 'U') IS NULL
               CREATE TABLE plantas (
                   id INT IDENTITY(1,1) PRIMARY KEY,
                   nombre NVARCHAR(255) NOT NULL, -- Añadimos NOT NULL para mejor práctica
                   tipo NVARCHAR(255) NOT NULL   -- Añadimos NOT NULL
               )");

    // 2. Insertar planta si se envió el formulario (método POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Limpiar y validar los datos recibidos
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);

        // Asegurarnos de que los campos no estén vacíos después de la validación
        if ($nombre && $tipo) {
            $stmt = $db->prepare("INSERT INTO plantas (nombre, tipo) VALUES (?, ?)");
            $stmt->execute([$nombre, $tipo]);
            // Redirigir después de insertar para evitar reenvío del formulario si se refresca la página
            header("Location: index.php"); 
            exit();
        }
    }

    // 3. Obtener todas las plantas para mostrarlas en la lista
    $stmt_plantas = $db->query("SELECT id, nombre, tipo FROM plantas");
    $plantas = $stmt_plantas->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // En un entorno de producción, no mostrarías el mensaje de error detallado.
    // Aquí es útil para depurar.
    die("Error de conexión o consulta a la base de datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Plantas Nativas de Chile</title>
    <style>
        /* Estilos básicos para mejorar la apariencia */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #28a745; /* Un tono verde para las plantas */
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            background-color: #f1f1f1;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="text"] {
            width: calc(100% - 22px); /* Ajusta el ancho considerando el padding */
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
        }
        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background-color: #e9ecef;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 4px;
            border-left: 5px solid #28a745;
        }
        li span { /* Estilo opcional para añadir detalles */
            color: #666;
            font-size: 0.9em;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administración de Plantas Nativas</h1>

        <h2>Agregar Nueva Planta</h2>
        <form method="POST" action="index.php"> <label>Nombre de la Planta: <input type="text" name="nombre" required></label><br>
            <label>Tipo de Planta (Ej: Arbusto, Árbol, Hierba): <input type="text" name="tipo" required></label><br>
            <button type="submit">Guardar Planta</button>
        </form>

        <h2>Lista de Plantas Registradas</h2>
        <?php if (empty($plantas)): ?>
            <p>No hay plantas registradas aún.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($plantas as $planta): ?>
                    <li>
                        <?= htmlspecialchars($planta['nombre']) ?> <span>(<?= htmlspecialchars($planta['tipo']) ?>)</span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>