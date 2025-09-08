<?php

namespace App\Models;

use App\Config\Database;

class Empleado
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM empleados ORDER BY departamento, nombre");
        return $stmt->fetchAll();
    }

    public function save($datos)
    {
        $sql = "INSERT INTO empleados (nombre, salario, departamento) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$datos['nombre'], $datos['salario'], $datos['departamento']]);
    }

    public function promedioSalarioPorDepartamento()
    {
        $sql = "SELECT departamento, AVG(salario) as promedio_salario 
                FROM empleados 
                GROUP BY departamento 
                ORDER BY promedio_salario DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function departamentoConMayorPromedio()
    {
        $sql = "SELECT departamento, AVG(salario) as promedio_salario 
                FROM empleados 
                GROUP BY departamento 
                ORDER BY promedio_salario DESC 
                LIMIT 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetch();
    }

    public function empleadosSobrePromedioDepartamento()
    {
        $sql = "SELECT e.*, dept_avg.promedio_departamento
                FROM empleados e
                JOIN (
                    SELECT departamento, AVG(salario) as promedio_departamento
                    FROM empleados
                    GROUP BY departamento
                ) dept_avg ON e.departamento = dept_avg.departamento
                WHERE e.salario > dept_avg.promedio_departamento
                ORDER BY e.departamento, e.salario DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Método matemático: Calcular salario neto con deducciones colombianas 2025
    public function calcularSalarioNeto($salarioBruto)
    {
        // Deducciones según ley colombiana 2025
        $salud = $salarioBruto * 0.04; // 4% salud
        $pension = $salarioBruto * 0.04; // 4% pensión
        
        // Retención en la fuente según tabla 2025 (UVT 2025 = $47,065)
        $uvt = 47065;
        $retencion = 0;
        
        // Calcular UVT del salario
        $salarioEnUvt = $salarioBruto / $uvt;
        
        if ($salarioEnUvt > 95 && $salarioEnUvt <= 150) {
            // Entre 95 y 150 UVT: 19% sobre exceso de 95 UVT
            $excesoUvt = $salarioEnUvt - 95;
            $retencion = ($excesoUvt * $uvt) * 0.19;
        } elseif ($salarioEnUvt > 150 && $salarioEnUvt <= 360) {
            // Entre 150 y 360 UVT: 28% sobre exceso de 150 UVT + valor anterior
            $baseAnterior = (150 - 95) * $uvt * 0.19; // 55 UVT al 19%
            $excesoUvt = $salarioEnUvt - 150;
            $retencion = $baseAnterior + (($excesoUvt * $uvt) * 0.28);
        } elseif ($salarioEnUvt > 360) {
            // Más de 360 UVT: 33% sobre exceso de 360 UVT + valores anteriores
            $baseAnterior1 = (150 - 95) * $uvt * 0.19; // 55 UVT al 19%
            $baseAnterior2 = (360 - 150) * $uvt * 0.28; // 210 UVT al 28%
            $excesoUvt = $salarioEnUvt - 360;
            $retencion = $baseAnterior1 + $baseAnterior2 + (($excesoUvt * $uvt) * 0.33);
        }
        
        // Solo aplicar retención si supera las 95 UVT (aproximadamente $4.5M en 2025)
        if ($salarioEnUvt <= 95) {
            $retencion = 0;
        }

        $totalDeducciones = $salud + $pension + $retencion;
        $salarioNeto = $salarioBruto - $totalDeducciones;

        return [
            'salario_bruto' => $salarioBruto,
            'salario_en_uvt' => round($salarioEnUvt, 2),
            'deduccion_salud' => $salud,
            'deduccion_pension' => $pension,
            'retencion_fuente' => $retencion,
            'total_deducciones' => $totalDeducciones,
            'salario_neto' => $salarioNeto
        ];
    }

    // Método matemático: Conversión de temperatura
    public function convertirTemperatura($valor, $unidadOrigen, $unidadDestino)
    {
        // Convertir todo a Celsius primero
        $celsius = 0;
        switch (strtoupper($unidadOrigen)) {
            case 'F':
                $celsius = ($valor - 32) * 5/9;
                break;
            case 'K':
                $celsius = $valor - 273.15;
                break;
            case 'C':
            default:
                $celsius = $valor;
                break;
        }

        // Convertir desde Celsius a la unidad destino
        switch (strtoupper($unidadDestino)) {
            case 'F':
                return ($celsius * 9/5) + 32;
            case 'K':
                return $celsius + 273.15;
            case 'C':
            default:
                return $celsius;
        }
    }
}
