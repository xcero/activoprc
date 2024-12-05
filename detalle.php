<!DOCTYPE html>
<html lang="es">
<head>
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}
?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>Detalle del Activo Fijo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        select, input[type="submit"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        img {
            width: 150px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .field {
            margin-bottom: 10px;
            text-align: left;
        }

        .back-button {
            margin-top: 20px;
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }

        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            margin: auto;
            display: block;
            width: 400px; /* Tamaño fijo */
            height: 400px; /* Tamaño fijo */
            object-fit: cover; /* Ajustar la imagen al tamaño especificado */
        }

        .close {
            position: absolute;
            top: 10px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Estilo para permitir resize */
        .resizable-img {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <img src="data/Logo_PRC.png" alt="Logo">
    <h1>Detalle del Activo Fijo</h1>

    <!-- Formulario para seleccionar el código -->
    <form method="GET" action="detalle.php">
        <label for="codigo">Seleccione un Código:</label>
        <select name="codigo" id="codigo" required>
            <option value="">--Seleccione--</option>
            <?php
            include 'config.php';
            $conn->set_charset("utf8mb4");

            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            $sql = "SELECT codigo FROM activo_fijo";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['codigo']) . "'>" . htmlspecialchars($row['codigo']) . "</option>";
                }
            } else {
                echo "<option value=''>No hay activos disponibles</option>";
            }
            ?>
        </select>
        <input type="submit" value="Consultar">
    </form>

    <?php
    // Verificar si se seleccionó un código para mostrar los detalles
    if (isset($_GET['codigo']) && !empty($_GET['codigo'])) {
        $codigo = $_GET['codigo'];

        $stmt = $conn->prepare("SELECT * FROM activo_fijo WHERE codigo = ?");
        $stmt->bind_param("s", $codigo);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $activo = $result->fetch_assoc();
            ?>

            <div class="field">
                <label>Código:</label>
                <span><?php echo htmlspecialchars($activo['codigo']); ?></span>
            </div>

            <div class="field">
                <label>Fecha de Compra:</label>
                <span><?php echo htmlspecialchars($activo['fechacompra']); ?></span>
            </div>

            <div class="field">
                <label>Asignación:</label>
                <span><?php echo htmlspecialchars($activo['asignacion']); ?></span>
            </div>

             <div class="field">
                    <label for="codigo_consultor" class="form-label">Código Consultor:</label>
                    <span><?php echo htmlspecialchars($activo['codigo_consultor']); ?></span> 
             </div>

            <div class="field">
                <label>Ubicación:</label>
                <span><?php echo htmlspecialchars($activo['ubicacion']); ?></span>
            </div>

            <div class="field">
                <label>Modelo:</label>
                <span><?php echo htmlspecialchars($activo['modelo']); ?></span>
            </div>

            <div class="field">
                <label>Serie:</label>
                <span><?php echo htmlspecialchars($activo['serie']); ?></span>
            </div>

            <div class="field">
                <label>Tipo de Activo:</label>
                <span><?php echo htmlspecialchars($activo['tipoactivo']); ?></span>
            </div>

            <div class="field">
                <label>Descripción:</label>
                <span><?php echo htmlspecialchars($activo['descripcion']); ?></span>
            </div>

            <div class="field">
                <label>Estado:</label>
                <span><?php echo htmlspecialchars($activo['estado']); ?></span>
            </div>

            <div class="field">
                <label>Valor de Adquisición:</label>
                <span>$<?php echo number_format($activo['valoradquisicion'], 2); ?></span>
            </div>

            <?php if (!empty($activo['imagen'])): ?>
                <div class="field">
                    <label>Imagen:</label>
                    <img class="resizable-img" src="data:image/jpeg;base64,<?php echo base64_encode($activo['imagen']); ?>" alt="Imagen del Activo" onclick="openModal(this)">
                </div>
            <?php else: ?>
                <p><em>No hay imagen disponible para este activo.</em></p>
            <?php endif; ?>

            <?php
        } else {
            echo "<p>No se encontró el activo seleccionado.</p>";
        }
        $stmt->close();
    }

    $conn->close();
    ?>

    <a href="listado.php" class="back-button">Volver al Listado</a>
</div>

<!-- Modal para ver la imagen ampliada -->
<div id="myModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <img class="modal-content" id="modal-img">
</div>

<script>
    function openModal(img) {
        var modal = document.getElementById('myModal');
        var modalImg = document.getElementById('modal-img');
        modal.style.display = "block";
        modalImg.src = img.src;
    }

    function closeModal() {
        var modal = document.getElementById('myModal');
        modal.style.display = "none";
    }
</script>

</body>
</html>
