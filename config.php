<?php
// Configuración de la base de datos en Google Cloud SQL
$servername = "34.23.157.126"; // Cambia esto a la IP pública de tu instancia
$username = "admin"; // Nombre de usuario
$password = "Ache676seam157"; // Contraseña de la base de datos
$dbname = "prcactivofijo"; // Nombre de la base de datos

// Crear la conexión (solo una vez)
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Establecer el conjunto de caracteres a UTF-8
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

?>