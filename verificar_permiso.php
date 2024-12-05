<?php
session_start();

function verificar_permiso($roles_permitidos) {
    if (!isset($_SESSION['rol']) || !in_array($_SESSION['rol'], $roles_permitidos)) {
        header("Location: index.php");
        exit();
    }
}
