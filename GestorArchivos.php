<?php
class GestorArchivos {
    private $directorio;
    private $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    private $mimesPermitidos = ['application/pdf', 'image/jpeg', 'image/png'];
    private $tamanoMaximo = 5 * 1024 * 1024; // 5 MB

    public function __construct($directorio = 'uploads/') {
        $this->directorio = rtrim($directorio, '/') . '/';
        if (!is_dir($this->directorio)) {
            mkdir($this->directorio, 0755, true);
        }
    }


   public function subir($archivo, $titulo = 'archivo') {
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return "Error al subir el archivo.";
        }

        if ($archivo['size'] > $this->tamanoMaximo) {
            return "El archivo excede el tamaño máximo permitido (5MB).";
        }

        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->extensionesPermitidas)) {
            return "Extensión de archivo no permitida.";
        }

        // Validar Tipo MIME real
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($archivo['tmp_name']);
        if (!in_array($mimeReal, $this->mimesPermitidos)) {
            return "El contenido del archivo no coincide con su extensión.";
        }

        // Limpiamos el título para que no tenga caracteres raros
        $tituloLimpio = preg_replace('/[^A-Za-z0-9_\-\s]/', '_', $titulo);

        // Generamos el nombre seguro original que tú tenías
        $nombreSeguroOriginal = bin2hex(random_bytes(10)) . '_' . time() . '.' . $extension;

        // Unimos ambos con el separador especial '___' sin alterar tu lógica segura original
        $nuevoNombreCompleto = $tituloLimpio . '___' . $nombreSeguroOriginal;
        $rutaDestino = $this->directorio . $nuevoNombreCompleto;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return true; 
        }

        return "No se pudo guardar el archivo.";
    }

    public function listar() {
        $archivos = [];
        if ($idDir = opendir($this->directorio)) {
            while (($archivo = readdir($idDir)) !== false) {
                if ($archivo != '.' && $archivo != '..') {
                    $rutaCompleta = $this->directorio . $archivo;
                    $archivos[] = [
                        'nombre' => $archivo,
                        'tamano' => round(filesize($rutaCompleta) / 1024, 2) . ' KB',
                        'fecha' => date("Y-m-d H:i:s", filemtime($rutaCompleta))
                    ];
                }
            }
            closedir($idDir);
        }
        return $archivos;
    }

    public function eliminar($nombre) {
        // SEGURIDAD: Evitar Path Traversal limpiando el nombre
        $nombreLimpio = basename($nombre); 
        $rutaCompleta = $this->directorio . $nombreLimpio;

        if (file_exists($rutaCompleta) && is_file($rutaCompleta)) {
            if (unlink($rutaCompleta)) {
                return true;
            }
            return "No se pudo eliminar el archivo.";
        }
        return "Archivo no encontrado.";
    }
}