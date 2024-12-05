  <!DOCTYPE html> 
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">

    <?php
    session_start();
    if ($_SESSION['rol'] !== 'admin') {

    header("Location: error.php"); // Redirige a una página de error personalizada

    exit();
}
    ?>

    <title>Consulta de Activos por Asignación</title>
</head>
<body>

    <h1>Consulta de Activos Fijos Agrupados por Asignación</h1>
    <a href="listado.php">Volver al Listado</a> | 
    <a href="logout.php">Cerrar Sesión</a>

    <!-- Botón para actualizar la página -->
    <form method="GET" action="">
        <button type="submit">Actualizar Página</button>
    </form>

    <!-- Formulario para Filtrado por Asignación -->
    <form method="GET" action="">
        <label for="asignacion">Asignación:</label>
        <input type="text" name="asignacion" id="asignacion">
        <button type="submit">Filtrar</button>
    </form>

    <table border="1">
        <tr>
            <th>Asignación</th>
            <th>CódigoConsultor</th>
            <th>Código</th>
            <th>Fecha de Compra</th>
            <th>Ubicación</th>
            <th>Modelo</th>
            <th>Serie</th>
            <th>Tipo de Activo</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Valor de Adquisición</th>
            <th>Imagen</th>
        </tr>

        <?php
        // Conexión a la base de datos
        include 'config.php';
        $conn->set_charset("utf8mb4");

        // Comprobar la conexión
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Variable de filtro por Asignación
        $asignacion = isset($_GET['asignacion']) ? $_GET['asignacion'] : '';

        // Construir la consulta SQL con agrupación
        $sql = "SELECT asignacion, codigo,codigo_consultor, fechacompra, ubicacion, modelo, serie, tipoactivo, descripcion, estado, valoradquisicion, imagen 
                FROM activo_fijo WHERE 1=1";

        // Filtrar por Asignación si se proporciona
        if (!empty($asignacion)) {
            $sql .= " AND asignacion LIKE '%" . $conn->real_escape_string($asignacion) . "%'";
        }

        // Agrupar por Asignación
        $sql .= " ORDER BY asignacion";

        // Ejecutar la consulta
        $result = $conn->query($sql);

        // Comprobar si hay resultados
        if ($result->num_rows > 0) {
            $currentAsignacion = '';
            while ($row = $result->fetch_assoc()) {
                // Si cambia la asignación, muestra una nueva fila de encabezado
                if ($currentAsignacion !== $row['asignacion']) {
                    $currentAsignacion = $row['asignacion'];
                    echo "<tr>
                            <td colspan='11' style='background-color: #f0f0f0; font-weight: bold;'>Asignación: " . $currentAsignacion . "</td>
                          </tr>";
                }
                
                // Mostrar cada fila de resultados
                echo "<tr>
                        <td>" . $row['asignacion'] . "</td>
                        <td>" . $row['codigo_consultor'] . "</td>
                        <td>" . $row['codigo'] . "</td>
                        <td>" . $row['fechacompra'] . "</td>
                        <td>" . $row['ubicacion'] . "</td>
                        <td>" . $row['modelo'] . "</td>
                        <td>" . $row['serie'] . "</td>
                        <td>" . $row['tipoactivo'] . "</td>
                        <td>" . $row['descripcion'] . "</td>
                        <td>" . $row['estado'] . "</td>
                        <td>$" . number_format($row['valoradquisicion'], 2) . "</td>";

                // Mostrar la imagen si existe
                if (!empty($row['imagen'])) {
                    $mimeType = 'image/jpeg'; // Ajusta según el tipo de imagen
                    $imageData = base64_encode($row['imagen']);
                    echo "<td><img src='data:$mimeType;base64,$imageData' alt='Imagen' width='100'></td>";
                } else {
                    echo "<td>No hay imagen</td>";
                }
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No se encontraron activos fijos.</td></tr>";
        }

        // Cerrar la conexión
        $conn->close();
        ?>
    </table>

    <!-- Botón para exportar a Excel -->
    <form method="post" action="exportar_excel.php">
        <button type="submit">Exportar a Excel</button>
    </form>
    <form method="POST" action="acta.php">
    <!-- Pasar el filtro de asignación al archivo acta.php -->
    <input type="hidden" name="asignacion" value="<?php echo $asignacion; ?>">
    <button type="submit">Generar Acta en Word</button>
</form>
</body>
</html>