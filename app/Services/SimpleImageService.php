<?php

namespace App\Services;

class SimpleImageService
{
    private $uploadsPath;

    public function __construct()
    {
        // Definir el directorio de uploads
        $this->uploadsPath = __DIR__ . '/../../uploads/';
        
        // Crear el directorio si no existe
        if (!file_exists($this->uploadsPath)) {
            mkdir($this->uploadsPath, 0755, true);
        }
        
        // Crear subdirectorios
        $subdirs = ['empleados', 'productos', 'reportes', 'thumbnails'];
        foreach ($subdirs as $subdir) {
            $path = $this->uploadsPath . $subdir;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }
    }

    /**
     * Procesar foto de empleado de manera simple
     */
    public function procesarFotoEmpleado($archivoImagen, $empleadoId)
    {
        try {
            // Validar el archivo
            $validacion = $this->validarImagen($archivoImagen);
            if (isset($validacion['error'])) {
                return $validacion;
            }

            // Generar nombre único
            $extension = strtolower(pathinfo($archivoImagen['name'], PATHINFO_EXTENSION));
            $nombreArchivo = 'empleado_' . $empleadoId . '_' . time() . '.' . $extension;
            $rutaCompleta = $this->uploadsPath . 'empleados/' . $nombreArchivo;

            // Mover el archivo subido
            if (move_uploaded_file($archivoImagen['tmp_name'], $rutaCompleta)) {
                return [
                    'success' => 'Imagen procesada correctamente',
                    'archivo' => $nombreArchivo,
                    'ruta' => 'uploads/empleados/' . $nombreArchivo
                ];
            } else {
                return ['error' => 'Error al mover el archivo'];
            }

        } catch (\Exception $e) {
            return ['error' => 'Error al procesar imagen: ' . $e->getMessage()];
        }
    }

    /**
     * Validar imagen
     */
    private function validarImagen($archivo)
    {
        // Verificar que se haya subido un archivo
        if (!isset($archivo['tmp_name']) || empty($archivo['tmp_name'])) {
            return ['error' => 'No se ha seleccionado ningún archivo'];
        }

        // Verificar errores de upload
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Error al subir el archivo'];
        }

        // Verificar tamaño (max 2MB para coincidir con PHP)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return ['error' => 'El archivo es demasiado grande (máximo 2MB)'];
        }

        // Verificar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($tipoMime, $tiposPermitidos)) {
            return ['error' => 'Tipo de archivo no permitido. Use JPG, PNG, GIF o WebP'];
        }

        return ['success' => 'Archivo válido'];
    }
}
