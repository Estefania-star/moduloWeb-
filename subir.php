<?php
require_once 'GestorArchivos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    // Recibimos el título y limpiamos espacios vacíos. Si no ponen nada, se usa 'archivo'
    $titulo = !empty($_POST['titulo']) ? trim($_POST['titulo']) : 'archivo';
    
    $gestor = new GestorArchivos();
    // Le pasamos el archivo y el título al método subir
    $resultado = $gestor->subir($_FILES['archivo'], $titulo);

    if ($resultado === true) {
        $_SESSION['mensaje'] = "¡Archivo subido con éxito y renombrado de forma segura!";
    } else {
        $_SESSION['error'] = $resultado;
    }
}

header('Location: index.php');
exit;