
<?php
session_start();
include 'config.php';
include 'verificar_permiso.php'; // Asegúrate de definir correctamente este archivo

// Verificar permisos
if ($_SESSION['rol'] !== 'admin') {
    header("Location: error.php");
    exit();
}

// Función para mostrar alertas
function mostrarAlerta($mensaje, $tipo = "success") {
    echo "<div class='alert alert-{$tipo} mt-3'>{$mensaje}</div>";
}

// Función para procesar la imagen
function procesarImagen($imagen) {
    if (isset($imagen['tmp_name']) && $imagen['tmp_name'] !== '') {
        $fileSize = filesize($imagen['tmp_name']);
        $mimeType = mime_content_type($imagen['tmp_name']);
        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
            return "El archivo subido no es una imagen válida.";
        } elseif ($fileSize > 2097152) { // 2MB límite
            return "El archivo supera el tamaño máximo permitido (2MB).";
        } else {
            return file_get_contents($imagen['tmp_name']);
        }
    }
    return null;
}

// Procesar la actualización o inserción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $codigo = $data['codigo'] ?? '';
    $accion = $data['accion'] ?? ''; // Puede ser 'insertar' o 'actualizar'
    
    // Procesar imagen si existe
    $error = null;
    $hexData = null;
    if (!empty($_FILES['imagen']['tmp_name'])) {
        $hexData = procesarImagen($_FILES['imagen']);
        if (is_string($hexData)) $error = $hexData;
    }

    if (!$error) {
        if ($accion === 'actualizar') {
            // SQL para actualización
            $sql = $hexData ? 
                "UPDATE activo_fijo SET fechacompra = ?, ubicacion = ?, modelo = ?, asignacion = ?, serie = ?, tipoactivo = ?, descripcion = ?, estado = ?, valoradquisicion = ?, imagen = ?, codigo_consultor = ? WHERE codigo = ?" :
                "UPDATE activo_fijo SET fechacompra = ?, ubicacion = ?, modelo = ?, asignacion = ?, serie = ?, tipoactivo = ?, descripcion = ?, estado = ?, valoradquisicion = ?, codigo_consultor = ? WHERE codigo = ?";
            
            $stmt = $conn->prepare($sql);
            if ($hexData) {
                $stmt->bind_param("ssssssssssss", $data['fechacompra'], $data['ubicacion'], $data['modelo'], $data['asignacion'], $data['serie'], $data['tipoactivo'], $data['descripcion'], $data['estado'], $data['valoradquisicion'], $hexData, $data['codigo_consultor'], $codigo);
            } else {
                $stmt->bind_param("sssssssssss", $data['fechacompra'], $data['ubicacion'], $data['modelo'], $data['asignacion'], $data['serie'], $data['tipoactivo'], $data['descripcion'], $data['estado'], $data['valoradquisicion'], $data['codigo_consultor'], $codigo);
            }
        } elseif ($accion === 'insertar') {
            // SQL para inserción
            $sql = "INSERT INTO activo_fijo (codigo, codigo_consultor, fechacompra, asignacion, ubicacion, modelo, serie, tipoactivo, descripcion, estado, valoradquisicion, imagen) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssssss", $data['codigo'], $data['codigo_consultor'], $data['fechacompra'], $data['asignacion'], $data['ubicacion'], $data['modelo'], $data['serie'], $data['tipoactivo'], $data['descripcion'], $data['estado'], $data['valoradquisicion'], $hexData);
        }

        if ($stmt->execute()) {
            mostrarAlerta($accion === 'insertar' ? "Activo fijo insertado correctamente." : "Activo actualizado con éxito.");
            // Registrar en los logs
            registrarLog($conn, "Activo {$codigo} " . ($accion === 'insertar' ? "insertado" : "actualizado") . ".", $_SESSION['usuario']);
        } else {
            mostrarAlerta("Error: " . $stmt->error, "danger");
        }
        $stmt->close();
    } else {
        mostrarAlerta($error, "danger");
    }
}

$conn->close();
?>
