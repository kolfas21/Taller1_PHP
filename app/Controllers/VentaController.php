<?php

namespace App\Controllers;

use App\Models\Venta;
use App\Services\MailService;
use App\Services\ImageService;
use Dompdf\Dompdf;
use Dompdf\Options;

class VentaController
{
    private $ventaModel;
    private $mailService;
    private $imageService;

    public function __construct()
    {
        $this->ventaModel = new Venta();
        $this->mailService = new MailService();
        $this->imageService = new ImageService();
    }

    public function index()
    {
        $ventas = $this->ventaModel->getAll();
        $totalVentas = $this->ventaModel->totalVentas();
        $clienteQueMasGasto = $this->ventaModel->clienteQueMasGasto();
        $productoMasVendido = $this->ventaModel->productoMasVendido();
        $resumenVentas = $this->ventaModel->resumenVentas();

        return [
            'ventas' => $ventas,
            'total_ventas' => $totalVentas,
            'cliente_mas_gasto' => $clienteQueMasGasto,
            'producto_mas_vendido' => $productoMasVendido,
            'resumen_ventas' => $resumenVentas
        ];
    }

    public function store($datos)
    {
        $errores = [];

        if (empty($datos['cliente'])) {
            $errores[] = 'El cliente es obligatorio';
        }
        if (empty($datos['producto'])) {
            $errores[] = 'El producto es obligatorio';
        }
        if (!is_numeric($datos['cantidad']) || $datos['cantidad'] <= 0) {
            $errores[] = 'La cantidad debe ser un número positivo';
        }
        if (!is_numeric($datos['precio_unitario']) || $datos['precio_unitario'] <= 0) {
            $errores[] = 'El precio unitario debe ser un número positivo';
        }
        if (empty($datos['fecha_venta'])) {
            $errores[] = 'La fecha de venta es obligatoria';
        }

        if (!empty($errores)) {
            return ['error' => implode(', ', $errores)];
        }

        $resultado = $this->ventaModel->save($datos);
        
        if ($resultado) {
            // Procesar imagen del producto si se subió una
            if (isset($_FILES['imagen_producto']) && $_FILES['imagen_producto']['error'] === UPLOAD_ERR_OK) {
                $ventaId = $this->ventaModel->getLastInsertId();
                $resultadoImagen = $this->imageService->procesarImagenProducto($_FILES['imagen_producto'], $ventaId);
                
                if (isset($resultadoImagen['success'])) {
                    $this->ventaModel->actualizarImagen($ventaId, $resultadoImagen['ruta']);
                }
            }

            // Enviar notificación de venta si hay email de administrador configurado
            $adminEmail = getenv('ADMIN_EMAIL');
            if ($adminEmail) {
                $ventaData = [
                    'producto' => $datos['producto'],
                    'cantidad' => $datos['cantidad'],
                    'precio' => $datos['precio_unitario'],
                    'total' => $datos['cantidad'] * $datos['precio_unitario']
                ];
                
                $this->mailService->enviarNotificacionVenta($adminEmail, $ventaData);
            }

            return ['success' => 'Venta agregada exitosamente'];
        } else {
            return ['error' => 'Error al agregar venta'];
        }
    }

    public function calcularInteresCompuesto($capital, $tasa, $tiempo, $periodos = 1)
    {
        if (!is_numeric($capital) || $capital <= 0) {
            return ['error' => 'El capital debe ser un número positivo'];
        }
        if (!is_numeric($tasa) || $tasa <= 0) {
            return ['error' => 'La tasa debe ser un número positivo'];
        }
        if (!is_numeric($tiempo) || $tiempo <= 0) {
            return ['error' => 'El tiempo debe ser un número positivo'];
        }
        if (!is_numeric($periodos) || $periodos <= 0) {
            return ['error' => 'Los periodos deben ser un número positivo'];
        }

        return $this->ventaModel->calcularInteresCompuesto($capital, $tasa / 100, $tiempo, $periodos);
    }

    public function convertirVelocidad($valor, $origen, $destino)
    {
        if (!is_numeric($valor) || $valor < 0) {
            return ['error' => 'El valor debe ser un número positivo o cero'];
        }

        $unidadesPermitidas = ['m/s', 'ms', 'km/h', 'kmh', 'mph'];
        $origenLower = strtolower($origen);
        $destinoLower = strtolower($destino);

        if (!in_array($origenLower, $unidadesPermitidas) || !in_array($destinoLower, $unidadesPermitidas)) {
            return ['error' => 'Unidades permitidas: m/s, km/h, mph'];
        }

        $resultado = $this->ventaModel->convertirVelocidad($valor, $origen, $destino);
        
        return [
            'valor_original' => $valor,
            'unidad_origen' => $origen,
            'valor_convertido' => round($resultado, 2),
            'unidad_destino' => $destino
        ];
    }

    public function generarPDFVentas()
    {
        $datos = $this->index();
        
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generarHTMLReporte($datos);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $dompdf->stream('reporte_ventas.pdf', array('Attachment' => false));
    }

    private function generarHTMLReporte($datos)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Reporte de Ventas</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; text-align: center; }
                h2 { color: #666; border-bottom: 1px solid #ccc; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f4f4f4; }
                .highlight { background-color: #ffffcc; }
            </style>
        </head>
        <body>
            <h1>Reporte de Ventas</h1>
            
            <h2>Resumen General</h2>
            <p><strong>Total de ventas realizadas:</strong> ' . $datos['total_ventas'] . '</p>';
            
        if ($datos['cliente_mas_gasto']) {
            $html .= '<p><strong>Cliente que más gastó:</strong> ' . $datos['cliente_mas_gasto']['cliente'] . 
                     ' ($' . number_format($datos['cliente_mas_gasto']['total_gastado'], 2) . ')</p>';
        }
        
        if ($datos['producto_mas_vendido']) {
            $html .= '<p><strong>Producto más vendido:</strong> ' . $datos['producto_mas_vendido']['producto'] . 
                     ' (' . $datos['producto_mas_vendido']['total_vendido'] . ' unidades)</p>';
        }
        
        $html .= '<h2>Resumen por Cliente</h2>
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Número de Compras</th>
                        <th>Total Gastado</th>
                        <th>Promedio por Compra</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($datos['resumen_ventas'] as $resumen) {
            $html .= '<tr>
                <td>' . $resumen['cliente'] . '</td>
                <td>' . $resumen['num_compras'] . '</td>
                <td>$' . number_format($resumen['total_gastado'], 2) . '</td>
                <td>$' . number_format($resumen['promedio_compra'], 2) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table></body></html>';
        
        return $html;
    }
}
