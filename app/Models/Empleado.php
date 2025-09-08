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

    // Método matemático: Calcular salario neto con deducciones colombianas
    public function calcularSalarioNeto($salarioBruto)
    {
        // Deducciones según ley colombiana (aproximadas)
        $salud = $salarioBruto * 0.04; // 4% salud
        $pension = $salarioBruto * 0.04; // 4% pensión
        
        // Retención en la fuente (simplificada)
        $retencion = 0;
        if ($salarioBruto > 4700000) { // Más de 4.7M
            $retencion = ($salarioBruto - 4700000) * 0.19;
        }

        $totalDeducciones = $salud + $pension + $retencion;
        $salarioNeto = $salarioBruto - $totalDeducciones;

        return [
            'salario_bruto' => $salarioBruto,
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
