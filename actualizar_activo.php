<?php 
session_start();
include 'config.php'; 
$conn->set_charset("utf8mb4");
include 'verificar_permiso.php'; // Verificación de permisos

// Verificar permisos
if ($_SESSION['rol'] !== 'admin') {
    header("Location: error.php");
    exit();
}

// Función para mostrar alertas
function mostrarAlerta($mensaje, $tipo = "success") {
    echo "<div class='alert alert-{$tipo} mt-3'>{$mensaje}</div>";
}

// Buscar activo si existe código (solo para actualización)
$activo = null;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $sql = "SELECT * FROM activo_fijo WHERE codigo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $activo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Activos</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestión de Activos Fijos</h1>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="actualizar_activo.php" class="card p-3 shadow-sm mb-4">
            <div class="mb-3">
                <label for="codigo" class="form-label">Buscar Activo por Código Inventario:</label>
                <input type="text" name="codigo" id="codigo" class="form-control" placeholder="Ingrese el código del activo">
            </div>
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </form>

        <!-- Mostrar alerta en caso de error -->
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Si existe un activo, mostramos los datos para actualizar -->
        <?php if (isset($activo)): ?>
            <h2 class="text-center">Actualizar Activo Fijo</h2>
            <form method="POST" enctype="multipart/form-data" action="actualizar.php" class="card p-4 shadow-sm">
                <input type="hidden" name="codigo" value="<?php echo $activo['codigo']; ?>">

                <!-- Información General -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Información General</legend>
                    <div class="mb-3">
                        <label for="fechacompra" class="form-label">Fecha de Compra:</label>
                        <input type="date" name="fechacompra" id="fechacompra" class="form-control" value="<?php echo $activo['fechacompra']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo_consultor" class="form-label">Código Consultor:</label>
                        <input type="text" name="codigo_consultor" id="codigo_consultor" class="form-control" value="<?php echo $activo['codigo_consultor']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="asignacion" class="form-label">Asignación:</label>
                        <input type="text" name="asignacion" id="asignacion" class="form-control" value="<?php echo $activo['asignacion']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Factura:</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="<?php echo $activo['ubicacion']; ?>" required>
                    </div>
                </fieldset>

                <!-- Detalles del Activo -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Detalles del Activo</legend>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo:</label>
                        <input type="text" name="modelo" id="modelo" class="form-control" value="<?php echo $activo['modelo']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="serie" class="form-label">Serie:</label>
                        <input type="text" name="serie" id="serie" class="form-control" value="<?php echo $activo['serie']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="tipoactivo" class="form-label">Tipo de Activo:</label>
                        <input type="text" name="tipoactivo" id="tipoactivo" class="form-control" value="<?php echo $activo['tipoactivo']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required><?php echo $activo['descripcion']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <input type="text" name="estado" id="estado" class="form-control" value="<?php echo $activo['estado']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="valoradquisicion" class="form-label">Valor de Adquisición:</label>
                        <input type="number" step="0.01" name="valoradquisicion" id="valoradquisicion" class="form-control" value="<?php echo $activo['valoradquisicion']; ?>" required>
                    </div>
                </fieldset>

                <!-- Imagen -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Imagen</legend>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Cargar Imagen:</label>
                        <input type="file" name="imagen" id="imagen" class="form-control">
                        <input type="hidden" name="imagen_base64" id="imagen_base64">
                    </div>
                </fieldset>

                <script>
                    // Código JavaScript para convertir la imagen a Base64
                    document.getElementById("imagen").addEventListener("change", function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Guardamos el resultado en el campo oculto
                                document.getElementById("imagen_base64").value = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="listado.php" class="btn btn-secondary">Volver al Listado</a>
                    <button type="submit" name="accion" value="actualizar" class="btn btn-success">Actualizar Activo</button>
                    <button type="submit" name="accion" value="insertar" class="btn btn-primary">Insertar Nuevo Activo</button>
                </div>
            </form>
        <?php else: ?>
            <!-- Si no se encuentra el activo, mostramos un formulario vacío para insertar un nuevo activo -->
            <h2 class="text-center">Insertar Nuevo Activo Fijo</h2>
            <form method="POST" enctype="multipart/form-data" action="actualizar.php" class="card p-4 shadow-sm">
                <!-- Campos para un nuevo activo -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Información General</legend>
                    <div class="mb-3">
                        <label for="fechacompra" class="form-label">Fecha de Compra:</label>
                        <input type="date" name="fechacompra" id="fechacompra" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="codigo_consultor" class="form-label">Código Consultor:</label>
                        <input type="text" name="codigo_consultor" id="codigo_consultor" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="asignacion" class="form-label">Asignación:</label>
                        <input type="text" name="asignacion" id="asignacion" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Factura:</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="form-control" required>
                    </div>
                </fieldset>

                <!-- Detalles del Activo -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Detalles del Activo</legend>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo:</label>
                        <input type="text" name="modelo" id="modelo" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="serie" class="form-label">Serie:</label>
                        <input type="text" name="serie" id="serie" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="tipoactivo" class="form-label">Tipo de Activo:</label>
                        <input type="text" name="tipoactivo" id="tipoactivo" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <input type="text" name="estado" id="estado" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="valoradquisicion" class="form-label">Valor de Adquisición:</label>
                        <input type="number" step="0.01" name="valoradquisicion" id="valoradquisicion" class="form-control" required>
                    </div>
                </fieldset>

                <!-- Imagen -->
                <fieldset class="mb-4">
                    <legend class="fw-bold">Imagen</legend>
                    <div class="mb-3">
                        <label for="imagen" class="form-label">Cargar Imagen:</label>
                        <input type="file" name="imagen" id="imagen" class="form-control">
                        <input type="hidden" name="imagen_base64" id="imagen_base64">
                    </div>
                </fieldset>

                <script>
                    // Código JavaScript para convertir la imagen a Base64
                    document.getElementById("imagen").addEventListener("change", function(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Guardamos el resultado en el campo oculto
                                document.getElementById("imagen_base64").value = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                </script>

                <!-- Botones -->
                <div class="d-flex justify-content-between">
                    <a href="listado.php" class="btn btn-secondary">Volver al Listado</a>
                    <button type="submit" name="accion" value="insertar" class="btn btn-primary">Insertar Nuevo Activo</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
