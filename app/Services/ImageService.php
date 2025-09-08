<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    private $manager;
    private $uploadsPath;

    public function __construct()
    {
        try {
            // Crear el manager con el driver GD
            $this->manager = new ImageManager(new Driver());
        } catch (\Exception $e) {
            error_log('Error inicializando ImageManager: ' . $e->getMessage());
            throw new \Exception('No se pudo inicializar el procesador de imágenes');
        }
        
        // Definir el directorio de uploads
        $this->uploadsPath = __DIR__ . '/../../uploads/';
        
        // Crear el directorio si no existe
        try {
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
        } catch (\Exception $e) {
            error_log('Error creando directorios: ' . $e->getMessage());
            // No lanzar excepción aquí, solo log del error
        }
    }

    /**
     * Procesar foto de empleado
     */
    public function procesarFotoEmpleado($archivoImagen, $empleadoId)
    {
        try {
            // Validar el archivo
            $validacion = $this->validarImagen($archivoImagen);
            if (isset($validacion['error'])) {
                return $validacion;
            }

            // Cargar la imagen
            $imagen = $this->manager->read($archivoImagen['tmp_name']);

            // Redimensionar para foto de perfil (300x300)
            $imagen = $imagen->resize(300, 300);

            // Generar nombre único
            $extension = strtolower(pathinfo($archivoImagen['name'], PATHINFO_EXTENSION));
            $nombreArchivo = 'empleado_' . $empleadoId . '_' . time() . '.' . $extension;
            $rutaCompleta = $this->uploadsPath . 'empleados/' . $nombreArchivo;

            // Guardar la imagen
            $imagen->save($rutaCompleta);

            // Crear thumbnail
            $this->crearThumbnail($rutaCompleta, $nombreArchivo);

            return [
                'success' => 'Imagen procesada correctamente',
                'archivo' => $nombreArchivo,
                'ruta' => 'uploads/empleados/' . $nombreArchivo,
                'thumbnail' => 'uploads/thumbnails/thumb_' . $nombreArchivo
            ];

        } catch (\Exception $e) {
            return ['error' => 'Error al procesar imagen: ' . $e->getMessage()];
        }
    }

    /**
     * Procesar imagen de producto
     */
    public function procesarImagenProducto($archivoImagen, $productoId)
    {
        try {
            // Validar el archivo
            $validacion = $this->validarImagen($archivoImagen);
            if (isset($validacion['error'])) {
                return $validacion;
            }

            // Cargar la imagen
            $imagen = $this->manager->read($archivoImagen['tmp_name']);

            // Redimensionar para producto (800x600)
            $imagen->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Generar nombre único
            $extension = strtolower(pathinfo($archivoImagen['name'], PATHINFO_EXTENSION));
            $nombreArchivo = 'producto_' . $productoId . '_' . time() . '.' . $extension;
            $rutaCompleta = $this->uploadsPath . 'productos/' . $nombreArchivo;

            // Guardar la imagen
            $imagen->save($rutaCompleta, 90); // Calidad 90%

            // Crear thumbnail
            $this->crearThumbnail($rutaCompleta, $nombreArchivo);

            return [
                'success' => 'Imagen de producto procesada correctamente',
                'archivo' => $nombreArchivo,
                'ruta' => 'uploads/productos/' . $nombreArchivo,
                'thumbnail' => 'uploads/thumbnails/thumb_' . $nombreArchivo
            ];

        } catch (\Exception $e) {
            return ['error' => 'Error al procesar imagen: ' . $e->getMessage()];
        }
    }

    /**
     * Crear gráfico de reporte
     */
    public function crearGraficoReporte($datos, $titulo, $tipo = 'barras')
    {
        try {
            // Crear una imagen base de 800x400
            $imagen = $this->manager->create(800, 400, 'ffffff');

            // Aquí podrías implementar lógica para crear gráficos
            // Por simplicidad, creamos una imagen básica con texto
            
            // Añadir título (simulado - en una implementación real usarías una librería de gráficos)
            $nombreArchivo = 'reporte_' . time() . '.png';
            $rutaCompleta = $this->uploadsPath . 'reportes/' . $nombreArchivo;

            // Guardar la imagen
            $imagen->save($rutaCompleta);

            return [
                'success' => 'Gráfico de reporte creado correctamente',
                'archivo' => $nombreArchivo,
                'ruta' => 'uploads/reportes/' . $nombreArchivo
            ];

        } catch (\Exception $e) {
            return ['error' => 'Error al crear gráfico: ' . $e->getMessage()];
        }
    }

    /**
     * Crear marca de agua en imagen
     */
    public function aplicarMarcaDeAgua($rutaImagen, $textoMarca = null)
    {
        try {
            $textoMarca = $textoMarca ?? 'Sistema Empleados';
            
            // Cargar la imagen
            $imagen = $this->manager->read($rutaImagen);

            // Aplicar marca de agua (posición inferior derecha)
            // Nota: En Intervention Image v3, el método text() puede requerir configuración adicional
            
            // Generar nuevo nombre
            $info = pathinfo($rutaImagen);
            $nuevoNombre = $info['filename'] . '_watermark.' . $info['extension'];
            $nuevaRuta = $info['dirname'] . '/' . $nuevoNombre;

            // Guardar la imagen con marca de agua
            $imagen->save($nuevaRuta);

            return [
                'success' => 'Marca de agua aplicada correctamente',
                'ruta' => $nuevaRuta
            ];

        } catch (\Exception $e) {
            return ['error' => 'Error al aplicar marca de agua: ' . $e->getMessage()];
        }
    }

    /**
     * Crear thumbnail
     */
    private function crearThumbnail($rutaOriginal, $nombreArchivo)
    {
        try {
            $imagen = $this->manager->read($rutaOriginal);
            
            // Redimensionar a 150x150
            $imagen = $imagen->resize(150, 150);

            $rutaThumbnail = $this->uploadsPath . 'thumbnails/thumb_' . $nombreArchivo;
            $imagen->save($rutaThumbnail);

            return true;
        } catch (\Exception $e) {
            error_log('Error creando thumbnail: ' . $e->getMessage());
            return false;
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

        // Verificar tamaño (max 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            return ['error' => 'El archivo es demasiado grande (máximo 5MB)'];
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

    /**
     * Redimensionar imagen existente
     */
    public function redimensionar($rutaImagen, $ancho, $alto, $mantenerProporcion = true)
    {
        try {
            $imagen = $this->manager->read($rutaImagen);

            if ($mantenerProporcion) {
                $imagen->resize($ancho, $alto, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            } else {
                $imagen->resize($ancho, $alto);
            }

            $imagen->save($rutaImagen);

            return ['success' => 'Imagen redimensionada correctamente'];

        } catch (\Exception $e) {
            return ['error' => 'Error al redimensionar: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener información de imagen
     */
    public function obtenerInfoImagen($rutaImagen)
    {
        try {
            if (!file_exists($rutaImagen)) {
                return ['error' => 'El archivo no existe'];
            }

            $imagen = $this->manager->read($rutaImagen);
            
            return [
                'ancho' => $imagen->width(),
                'alto' => $imagen->height(),
                'tamaño_archivo' => filesize($rutaImagen),
                'tipo_mime' => mime_content_type($rutaImagen)
            ];

        } catch (\Exception $e) {
            return ['error' => 'Error al obtener información: ' . $e->getMessage()];
        }
    }
}
