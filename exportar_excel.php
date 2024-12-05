<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=acta_distribucion_asignacion.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Conexión a la base de datos
include 'config.php';
include 'verificar_permiso.php';
verificar_permiso(['admin']);


// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta solo para los registros con 'asignacion'
$sql = "SELECT codigo, fechacompra, asignacion, ubicacion, modelo, serie, tipoactivo, descripcion, estado, valoradquisicion 
        FROM activo_fijo 
        WHERE asignacion IS NOT NULL";
$result = $conn->query($sql);

// Encabezados del reporte
echo "<table border='1' style='width:100%;'>";
echo "<tr><td colspan='10' align='center'><b>MINISTERIO DE AGRICULTURA Y GANADERIA</b></td></tr>";
echo "<tr><td colspan='10' align='center'><b>DIRECCION GENERAL DE DESARROLLO RURAL</b></td></tr>";
echo "<tr><td colspan='10' align='center'><b>PROGRAMA DE FORTALECIMIENTO DE LA RESILIENCIA CLIMATICA DE LOS BOSQUES CAFETALEROS EN EL SALVADOR</b></td></tr>";
echo "<tr><td colspan='10' align='center'><b>UNIDAD GERENCIAL DEL PROGRAMA</b></td></tr>";
echo "<tr><td colspan='10'></td></tr>";

// Información adicional del acta
echo "<tr><td colspan='5'><b>FUENTE DE FINANCIAMIENTO:</b> PRESTAMO EXTERNO BID 4870/OC-ES</td><td colspan='5' align='right'><b>FECHA:</b> " . date("d/m/Y") . "</td></tr>";
echo "<tr><td colspan='5'><b>COMPONENTE:</b> Administración, Monitoreo, Auditoría y Evaluación</td><td colspan='5'><b>CORRELATIVO:</b> 001/2023</td></tr>";
echo "<tr><td colspan='10'></td></tr>";

// Encabezados de la tabla de activos
echo "<tr>
        <th>Código</th>
        <th>Fecha de Compra</th>
        <th>Asignación</th>
        <th>Ubicación</th>
        <th>Modelo</th>
        <th>Serie</th>
        <th>Tipo de Activo</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Valor de Adquisición ($)</th>
    </tr>";

// Mostrar los datos de los activos fijos en la tabla
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['codigo']}</td>
                <td>{$row['fechacompra']}</td>
                <td>{$row['asignacion']}</td>
                <td>{$row['ubicacion']}</td>
                <td>{$row['modelo']}</td>
                <td>{$row['serie']}</td>
                <td>{$row['tipoactivo']}</td>
                <td>{$row['descripcion']}</td>
                <td>{$row['estado']}</td>
                <td>$" . number_format($row['valoradquisicion'], 2) . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='10'>No se encontraron activos asignados.</td></tr>";
}

// Sección de firmas
echo "<tr><td colspan='10'></td></tr>";
echo "<tr><td colspan='5'><b>ENTREGA:</b></td><td colspan='5'><b>RECIBE:</b></td></tr>";
echo "<tr><td colspan='5'>Juan Perez</td><td colspan='5'>Omar Osiris Reyes Romero</td></tr>";
echo "<tr><td colspan='5'>Administrador de ODC</td><td colspan='5'>Responsable del Bien</td></tr>";
echo "<tr><td colspan='10'></td></tr>";

// Sección de autorizaciones
echo "<tr><td colspan='3'><b>ELABORO:</b></td><td colspan='4'><b>Vo. Bo.:</b></td><td colspan='3'><b>AUTORIZA:</b></td></tr>";
echo "<tr>
        <td colspan='3'>Omar Reyes</td>
        <td colspan='4'>Sonia Reyes</td>
        <td colspan='3'>Hector Borja</td>
      </tr>";
echo "<tr>
        <td colspan='3'>Área de Contabilidad PRC</td>
        <td colspan='4'>Especialista Financiera PRC</td>
        <td colspan='3'>Gerencia General</td>
      </tr>";
echo "</table>";

// Cerrar la conexión a la base de datos
$conn->close();
?>
