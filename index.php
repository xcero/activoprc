<?php
include 'config.php';
session_start();

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validación del parámetro ID en la URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitizar el ID como un entero

    // Consulta para verificar si el ID existe
    $stmt = $conn->prepare("SELECT id FROM activos WHERE id = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    // Si no se encuentra el ID, redirigir al error.php con JavaScript
    if ($stmt->num_rows === 0) {
        echo '<script>window.location.href = "error.php";</script>';
        exit();
    }
    $stmt->close();
}

// Verificar si se envió el formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta para verificar usuario y contraseña
    $stmt = $conn->prepare("SELECT password, rol FROM usuarios WHERE usuario = ?");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $stmt->bind_result($password_guardado, $rol);
    $stmt->fetch();
    $stmt->close();

    // Verificar la contraseña
    if ($password_guardado && password_verify($password, $password_guardado)) {
        // Almacenar usuario y rol en la sesión
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $rol;

        // Redirigir al listado con JavaScript
        echo '<script>window.location.href = "listado.php";</script>';
        exit();
    } else {
        // Mostrar mensaje de error
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Activos Fijos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* Estilos integrados */
        body {
            font-family: Arial, sans-serif;
            background-color: #004d1a; /* Fondo verde corporativo */
            color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Asegura que ocupe toda la pantalla */
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
        }

        .login-box {
            background-color: #ffffff; /* Fondo blanco */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 350px;
            padding: 20px;
            text-align: center;
            color: #333;
        }

        .logo-img {
            max-width: 80px;
            margin-bottom: 15px;
        }

        h2, h3 {
            margin: 10px 0;
            color: #004d1a;
        }

        form {
            display: flex;
            flex-direction: column;
            margin-top: 15px;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #004d1a;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #003d14;
        }

        .error {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="data/Logo_PRC.png" alt="Logo" class="logo-img">
            </div>
            <h2>Sistema de Activo Fijo Interno</h2>
            <h3>Iniciar sesión</h3>
            <form method="POST" action="index.php">
                <label for="usuario">Usuario</label>
                <input type="text" name="usuario" id="usuario" required>
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
                <button type="submit" class="btn">Iniciar Sesión</button>
            </form>
            <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        </div>
    </div>
</body>
</html> 