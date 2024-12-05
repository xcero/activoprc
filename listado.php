<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    echo '<script>
        alert("Debe iniciar sesión para acceder a esta página.");
        window.location.href = "index.php";
    </script>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Listado de Activos Fijos</title>
</head>
<body>
    <div class="center-container">
        <div class="content-box">
            <h1>Listado de Activos Fijos</h1>
            <button onclick="location.reload();">Actualizar Página</button>
            <form method="GET" action="">
                <label for="codigo">Código:</label>
                <input type="text" id="codigo" name="codigo">
                <label for="consultor">Consultor:</label>
                <input type="text" id="consultor" name="consultor">
                <label for="tipoactivo">Tipo de Activo:</label>
                <input type="text" id="tipoactivo" name="tipoactivo">
                <label for="serie">Serie:</label>
                <input type="text" id="serie" name="serie">
                <button type="submit">Filtrar</button>
            </form>
            <div class="links">
                <ul>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="table-container">
        <h2>Tabla</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Fecha de Compra</th>
                    <th>Código Consultor</th>
                    <th>Ubicación</th>
                    <th>Modelo</th>
                    <th>Serie</th>
                    <th>Tipo de Activo</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Valor de Adquisición</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'config.php';
                $conn->set_charset("utf8mb4");

                // Variables de filtro
                $codigo = $_GET['codigo'] ?? '';
                $consultor = $_GET['consultor'] ?? '';
                $tipoactivo = $_GET['tipoactivo'] ?? '';
                $serie = $_GET['serie'] ?? '';

                // Consulta con parámetros seguros
                $sql = "SELECT codigo, fechacompra, codigo_consultor, ubicacion, modelo, serie, tipoactivo, descripcion, estado, valoradquisicion 
                        FROM activo_fijo WHERE 1=1";

                // Arreglo para los parámetros de la consulta
                $params = [];
                $types = ""; // Definir tipos de datos para los parámetros

                // Filtros dinámicos
                if (!empty($codigo)) {
                    $sql .= " AND codigo LIKE ?";
                    $params[] = "%$codigo%";
                    $types .= "s";
                }
                if (!empty($consultor)) {
                    $sql .= " AND codigo_consultor LIKE ?";
                    $params[] = "%$consultor%";
                    $types .= "s";
                }
                if (!empty($tipoactivo)) {
                    $sql .= " AND tipoactivo LIKE ?";
                    $params[] = "%$tipoactivo%";
                    $types .= "s";
                }
                if (!empty($serie)) {
                    $sql .= " AND serie LIKE ?";
                    $params[] = "%$serie%";
                    $types .= "s";
                }

                // Preparar la consulta
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    die("Error al preparar la consulta: " . $conn->error);
                }

                // Asignar los parámetros
                if (!empty($params)) {
                    $stmt->bind_param($types, ...$params);
                }

                // Ejecutar la consulta
                $stmt->execute();
                $result = $stmt->get_result();

                // Mostrar resultados
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['codigo']}</td>
                                <td>{$row['fechacompra']}</td>
                                <td>{$row['codigo_consultor']}</td>
                                <td>{$row['ubicacion']}</td>
                                <td>{$row['modelo']}</td>
                                <td>{$row['serie']}</td>
                                <td>{$row['tipoactivo']}</td>
                                <td>{$row['descripcion']}</td>
                                <td>{$row['estado']}</td>
                                <td>$" . number_format($row['valoradquisicion'], 2) . "</td>
                                <td>
                                    <a href='detalle.php?codigo={$row['codigo']}'>Detalle</a> |
                                    <a href='actualizar_activo.php?codigo={$row['codigo']}'>Actualizar</a> |
                                    <a href='consulta.php?codigo={$row['codigo']}'>Consulta</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>No se encontraron activos fijos.</td></tr>";
                }

                // Cerrar la conexión
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <form method="post" action="exportar_excel.php">
        <button type="submit">Exportar a Excel</button>
    </form>
</body>
</html>x|