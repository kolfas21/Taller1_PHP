<?php

namespace App\Controllers;

use App\Models\Empleado;
use App\Services\MailService;
use App\Services\ImageService;
use App\Services\SimpleImageService;

class EmpleadoController
{
    private $empleadoModel;
    private $mailService;
    private $imageService;

    public function __construct()
    {
        $this->empleadoModel = new Empleado();
        
        // Inicializar servicios con manejo de errores
        try {
            $this->mailService = new MailService();
        } catch (\Exception $e) {
            $this->mailService = null;
            error_log('Error inicializando MailService: ' . $e->getMessage());
        }
        
        try {
            $this->imageService = new ImageService();
        } catch (\Exception $e) {
            error_log('Error inicializando ImageService, usando SimpleImageService: ' . $e->getMessage());
            try {
                $this->imageService = new SimpleImageService();
            } catch (\Exception $e2) {
                $this->imageService = null;
                error_log('Error inicializando SimpleImageService: ' . $e2->getMessage());
            }
        }
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

        try {
            $resultado = $this->empleadoModel->save($datos);
            
            if ($resultado) {
                $empleadoId = $this->empleadoModel->getLastInsertId();
                
                // Procesar foto si se subió una
                if (isset($_FILES['foto_empleado']) && $this->imageService !== null) {
                    // Debug info
                    error_log('Archivo recibido: ' . print_r($_FILES['foto_empleado'], true));
                    
                    if ($_FILES['foto_empleado']['error'] === UPLOAD_ERR_OK) {
                        try {
                            $resultadoImagen = $this->imageService->procesarFotoEmpleado($_FILES['foto_empleado'], $empleadoId);
                            
                            if (isset($resultadoImagen['success'])) {
                                $this->empleadoModel->actualizarFoto($empleadoId, $resultadoImagen['ruta']);
                                error_log('Foto procesada exitosamente: ' . $resultadoImagen['ruta']);
                            } else {
                                error_log('Error en procesamiento: ' . ($resultadoImagen['error'] ?? 'Error desconocido'));
                            }
                        } catch (\Exception $e) {
                            // Si falla el procesamiento de imagen, continuar sin foto
                            error_log('Excepción procesando imagen: ' . $e->getMessage());
                        }
                    } else {
                        error_log('Error en upload: ' . $_FILES['foto_empleado']['error']);
                    }
                }

                // Enviar email de bienvenida si se proporciona un email
                if (!empty($datos['email']) && $this->mailService !== null) {
                    try {
                        $this->mailService->enviarBienvenidaEmpleado(
                            $datos['email'],
                            $datos['nombre'],
                            $datos['departamento'],
                            $datos['salario']
                        );
                    } catch (\Exception $e) {
                        // Si falla el envío de email, continuar sin email
                        error_log('Error enviando email: ' . $e->getMessage());
                    }
                }

                return ['success' => 'Empleado agregado exitosamente'];
            } else {
                return ['error' => 'Error al agregar empleado'];
            }
        } catch (\Exception $e) {
            error_log('Error en store: ' . $e->getMessage());
            return ['error' => 'Error interno del servidor'];
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

    /**
     * Procesar foto de empleado
     */
    public function procesarFoto($archivoImagen, $empleadoId)
    {
        return $this->imageService->procesarFotoEmpleado($archivoImagen, $empleadoId);
    }

    /**
     * Enviar email de bienvenida manual
     */
    public function enviarBienvenida($empleadoId, $email)
    {
        $empleado = $this->empleadoModel->getById($empleadoId);
        if (!$empleado) {
            return ['error' => 'Empleado no encontrado'];
        }

        return $this->mailService->enviarBienvenidaEmpleado(
            $email,
            $empleado['nombre'],
            $empleado['departamento'],
            $empleado['salario']
        );
    }

    /**
     * Generar reporte con gráfico
     */
    public function generarReporteConGrafico()
    {
        $datos = $this->index();
        
        // Crear gráfico con los datos
        $resultadoGrafico = $this->imageService->crearGraficoReporte(
            $datos['promedios_departamento'],
            'Promedio de Salarios por Departamento'
        );

        return $resultadoGrafico;
    }
}
