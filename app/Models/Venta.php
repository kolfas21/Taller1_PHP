<?php

namespace App\Models;

use App\Config\Database;

class Venta
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM ventas ORDER BY fecha_venta DESC");
        return $stmt->fetchAll();
    }

    public function save($datos)
    {
        $sql = "INSERT INTO ventas (cliente, producto, cantidad, precio_unitario, fecha_venta) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['cliente'], 
            $datos['producto'], 
            $datos['cantidad'], 
            $datos['precio_unitario'], 
            $datos['fecha_venta']
        ]);
    }

    public function totalVentas()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM ventas");
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function clienteQueMasGasto()
    {
        $sql = "SELECT cliente, SUM(cantidad * precio_unitario) as total_gastado
                FROM ventas
                GROUP BY cliente
                ORDER BY total_gastado DESC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    public function productoMasVendido()
    {
        $sql = "SELECT producto, SUM(cantidad) as total_vendido
                FROM ventas
                GROUP BY producto
                ORDER BY total_vendido DESC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    public function resumenVentas()
    {
        $sql = "SELECT 
                    cliente,
                    COUNT(*) as num_compras,
                    SUM(cantidad * precio_unitario) as total_gastado,
                    AVG(cantidad * precio_unitario) as promedio_compra
                FROM ventas
                GROUP BY cliente
                ORDER BY total_gastado DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Método matemático: Calcular interés compuesto
    public function calcularInteresCompuesto($capital, $tasa, $tiempo, $periodos = 1)
    {
        // Fórmula: A = P(1 + r/n)^(nt)
        // A = monto final, P = capital inicial, r = tasa anual, n = periodos por año, t = tiempo en años
        $montoFinal = $capital * pow((1 + ($tasa / $periodos)), ($periodos * $tiempo));
        $interes = $montoFinal - $capital;

        return [
            'capital_inicial' => $capital,
            'tasa_anual' => $tasa * 100 . '%',
            'tiempo_años' => $tiempo,
            'periodos_año' => $periodos,
            'monto_final' => $montoFinal,
            'interes_ganado' => $interes,
            'porcentaje_ganancia' => (($interes / $capital) * 100)
        ];
    }

    // Método matemático: Conversión de velocidad
    public function convertirVelocidad($valor, $unidadOrigen, $unidadDestino)
    {
        // Convertir todo a m/s primero
        $metrosPorSegundo = 0;
        switch (strtolower($unidadOrigen)) {
            case 'kmh':
            case 'km/h':
                $metrosPorSegundo = $valor / 3.6;
                break;
            case 'mph':
                $metrosPorSegundo = $valor * 0.44704;
                break;
            case 'ms':
            case 'm/s':
            default:
                $metrosPorSegundo = $valor;
                break;
        }

        // Convertir desde m/s a la unidad destino
        switch (strtolower($unidadDestino)) {
            case 'kmh':
            case 'km/h':
                return $metrosPorSegundo * 3.6;
            case 'mph':
                return $metrosPorSegundo / 0.44704;
            case 'ms':
            case 'm/s':
            default:
                return $metrosPorSegundo;
        }
    }

    /**
     * Obtener último ID insertado
     */
    public function getLastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * Actualizar imagen del producto en venta
     */
    public function actualizarImagen($ventaId, $rutaImagen)
    {
        $sql = "UPDATE ventas SET imagen_producto = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$rutaImagen, $ventaId]);
    }
}
