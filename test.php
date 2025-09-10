<?php
$serverName = "server-bd-plantas.database.windows.net,1433";
$database = "bd-plantas-nativas";
$username = "rootbd";
$password = "Benjamin123.";

try {
    $db = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
?>
