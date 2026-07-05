<?php
require_once 'GestorArchivos.php';
session_start();

if (isset($_GET['archivo'])) {
    $gestor = new GestorArchivos();
    $resultado = $gestor->eliminar($_GET['archivo']);

    if ($resultado === true) {
        $_SESSION['mensaje'] = "Archivo eliminado correctamente.";
    } else {
        $_SESSION['error'] = $resultado;
    }
}

header('Location: index.php');
exit;