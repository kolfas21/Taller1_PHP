<?php
echo "=== Configuración de Upload de PHP ===\n";
echo "file_uploads: " . (ini_get('file_uploads') ? 'Habilitado' : 'Deshabilitado') . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";

echo "\n=== Extensiones de Imagen ===\n";
echo "GD: " . (extension_loaded('gd') ? 'Sí' : 'No') . "\n";
echo "Imagick: " . (extension_loaded('imagick') ? 'Sí' : 'No') . "\n";

echo "\n=== Permisos de Directorio ===\n";
$uploadsDir = __DIR__ . '/uploads';
echo "Directorio uploads existe: " . (file_exists($uploadsDir) ? 'Sí' : 'No') . "\n";
echo "Directorio uploads es escribible: " . (is_writable($uploadsDir) ? 'Sí' : 'No') . "\n";

if (!file_exists($uploadsDir)) {
    if (mkdir($uploadsDir, 0755, true)) {
        echo "Directorio uploads creado exitosamente\n";
    } else {
        echo "Error al crear directorio uploads\n";
    }
}
?>
