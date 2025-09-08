<?php

namespace App\Controllers;

use App\Models\Empleado;

class EmpleadoController
{
    private $empleadoModel;

    public function __construct()
    {
        $this->empleadoModel = new Empleado();
    }

    public function index()
    {
        $empleados = $this->empleadoModel->getAll();
        $promediosDepartamento = $this->empleadoModel->promedioSalarioPorDepartamento();
        $departamentoMayorPromedio = $this->empleadoModel->departamentoConMayorPromedio();
        $empleadosSobrePromedio = $this->empleadoModel->empleadosSobrePromedioDepartamento();

        return [
            'empleados' => $empleados,
            'promedios_departamento' => $promediosDepartamento,
            'departamento_mayor_promedio' => $departamentoMayorPromedio,
            'empleados_sobre_promedio' => $empleadosSobrePromedio
        ];
    }

    public function store($datos)
    {
        if (empty($datos['nombre']) || empty($datos['salario']) || empty($datos['departamento'])) {
            return ['error' => 'Todos los campos son obligatorios'];
        }

        if (!is_numeric($datos['salario']) || $datos['salario'] <= 0) {
            return ['error' => 'El salario debe ser un número positivo'];
        }

        $resultado = $this->empleadoModel->save($datos);
        
        if ($resultado) {
            return ['success' => 'Empleado agregado exitosamente'];
        } else {
            return ['error' => 'Error al agregar empleado'];
        }
    }

    public function calcularSalarioNeto($salarioBruto)
    {
        if (!is_numeric($salarioBruto) || $salarioBruto <= 0) {
            return ['error' => 'El salario debe ser un número positivo'];
        }

        return $this->empleadoModel->calcularSalarioNeto($salarioBruto);
    }

    public function convertirTemperatura($valor, $origen, $destino)
    {
        if (!is_numeric($valor)) {
            return ['error' => 'El valor debe ser numérico'];
        }

        $unidadesPermitidas = ['C', 'F', 'K'];
        if (!in_array(strtoupper($origen), $unidadesPermitidas) || !in_array(strtoupper($destino), $unidadesPermitidas)) {
            return ['error' => 'Unidades permitidas: C, F, K'];
        }

        $resultado = $this->empleadoModel->convertirTemperatura($valor, $origen, $destino);
        
        return [
            'valor_original' => $valor,
            'unidad_origen' => strtoupper($origen),
            'valor_convertido' => round($resultado, 2),
            'unidad_destino' => strtoupper($destino)
        ];
    }
}
