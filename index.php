<?php
// index.php
require_once 'GestorArchivos.php';
$gestor = new GestorArchivos();
$archivos = $gestor->listar();
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor Seguro de Archivos</title>
    <style>
        <?php include 'estilos.css'; ?>
    </style>
</head>
<body>

<header>
    <h1>Plataforma de Gestión de Documentos</h1>
</header>

<nav>
    <p>Inicio / Panel de Archivos</p>
</nav>

<section>
    <h2>Subir nuevo archivo</h2>
    
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="subir.php" method="POST" enctype="multipart/form-data">
        <label for="titulo" style="font-weight: 600; color: var(--texto-oscuro); display: block; margin-bottom: 8px;">Título o Descripción de la Evidencia:</label>
        <input type="text" name="titulo" id="titulo" placeholder="Ej. tarea 1, Certificado Legalizado..." required style="padding: 12px; width: 100%; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px; box-sizing: border-box; font-family: inherit;"><br>

        <label for="archivo" style="font-weight: 600; color: var(--texto-oscuro); display: block; margin-bottom: 8px;">Seleccione Archivo (PDF, JPG, PNG - Máx 5MB):</label>
        <input type="file" name="archivo" id="archivo" required><br>
        
        <button type="submit">Subir Archivo</button>
    </form>
</section>

<section>
    <h2>Archivos Disponibles</h2>
    <?php if (empty($archivos)): ?>
        <p>No hay archivos subidos aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Evidencia / Título</th>
                    <th>Nombre de Archivo Seguro</th>
                    <th>Tamaño</th>
                    <th>Fecha de Subida</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($archivos as $arc): 
                    // Separamos el título del nombre seguro usando el '___'
                    $partes = explode('___', $arc['nombre']);
                    
                    if (count($partes) > 1) {
                        $tituloMostrar = $partes[0];
                        $nombreArchivoMostrar = $partes[1];
                    } else {
                        // Por si acaso quedó algún archivo viejo sin el separador
                        $tituloMostrar = "Sin título";
                        $nombreArchivoMostrar = $arc['nombre'];
                    }
                ?>
                    <tr>
                        <td style="font-weight: bold; color: #5b4a8c;"><?= htmlspecialchars($tituloMostrar) ?></td>
                        <td style="font-family: monospace; font-size: 0.85rem; color: #7f8c8d;"><?= htmlspecialchars($nombreArchivoMostrar) ?></td>
                        <td><?= $arc['tamano'] ?></td>
                        <td><?= $arc['fecha'] ?></td>
                        <td>
                            <a href="uploads/<?= urlencode($arc['nombre']) ?>" download>Descargar</a> | 
                            <a class="btn-del" href="eliminar.php?archivo=<?= urlencode($arc['nombre']) ?>" onclick="return confirm('¿Seguro que deseas eliminar este archivo?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<footer>
    <p>&copy; <?= date('Y') ?> - Módulo Web de Archivos Seguro</p>
</footer>

</body>
</html>