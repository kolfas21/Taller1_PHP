<?php
session_start();

// Autoload de Composer
require_once 'vendor/autoload.php';

// Importar las clases necesarias
use App\Config\Database;
use App\Controllers\EmpleadoController;
use App\Controllers\VentaController;

// Inicializar la base de datos
$database = Database::getInstance();
$database->createTables();

// Inicializar controladores
$empleadoController = new EmpleadoController();
$ventaController = new VentaController();

// Variables para los resultados
$datosEmpleados = $empleadoController->index();
$datosVentas = $ventaController->index();
$resultadoSalario = null;
$resultadoInteres = null;
$resultadoTemperatura = null;
$resultadoVelocidad = null;

// Variable para recordar la pestaña activa
$tabActiva = $_SESSION['tab_activa'] ?? 'empleados';
// Limpiar la variable de sesión después de usarla
if (isset($_SESSION['tab_activa'])) {
    unset($_SESSION['tab_activa']);
}

// Procesar formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_empleado':
            $resultado = $empleadoController->store($_POST);
            if (isset($resultado['success'])) {
                $_SESSION['message'] = $resultado['success'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $resultado['error'];
                $_SESSION['message_type'] = 'error';
            }
            header('Location: index.php');
            exit;
            
        case 'add_venta':
            $resultado = $ventaController->store($_POST);
            if (isset($resultado['success'])) {
                $_SESSION['message'] = $resultado['success'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $resultado['error'];
                $_SESSION['message_type'] = 'error';
            }
            $_SESSION['tab_activa'] = 'ventas'; // Recordar que vamos a la pestaña ventas
            header('Location: index.php');
            exit;
            
        case 'calcular_salario':
            $resultadoSalario = $empleadoController->calcularSalarioNeto($_POST['salario_bruto']);
            if (isset($resultadoSalario['error'])) {
                $_SESSION['message'] = $resultadoSalario['error'];
                $_SESSION['message_type'] = 'error';
                $resultadoSalario = null;
            }
            $_SESSION['tab_activa'] = 'calculadoras'; // Recordar que vamos a la pestaña calculadoras
            break;
            
        case 'calcular_interes':
            $resultadoInteres = $ventaController->calcularInteresCompuesto(
                $_POST['capital'],
                $_POST['tasa'],
                $_POST['tiempo'],
                $_POST['periodos']
            );
            if (isset($resultadoInteres['error'])) {
                $_SESSION['message'] = $resultadoInteres['error'];
                $_SESSION['message_type'] = 'error';
                $resultadoInteres = null;
            }
            $_SESSION['tab_activa'] = 'calculadoras'; // Recordar que vamos a la pestaña calculadoras
            break;
            
        case 'convertir_temperatura':
            $resultadoTemperatura = $empleadoController->convertirTemperatura(
                $_POST['valor_temp'],
                $_POST['origen_temp'],
                $_POST['destino_temp']
            );
            if (isset($resultadoTemperatura['error'])) {
                $_SESSION['message'] = $resultadoTemperatura['error'];
                $_SESSION['message_type'] = 'error';
                $resultadoTemperatura = null;
            }
            $_SESSION['tab_activa'] = 'calculadoras'; // Recordar que vamos a la pestaña calculadoras
            break;
            
        case 'convertir_velocidad':
            $resultadoVelocidad = $ventaController->convertirVelocidad(
                $_POST['valor_vel'],
                $_POST['origen_vel'],
                $_POST['destino_vel']
            );
            if (isset($resultadoVelocidad['error'])) {
                $_SESSION['message'] = $resultadoVelocidad['error'];
                $_SESSION['message_type'] = 'error';
                $resultadoVelocidad = null;
            }
            $_SESSION['tab_activa'] = 'calculadoras'; // Recordar que vamos a la pestaña calculadoras
            break;
    }
}

// Manejar acciones GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'generar_pdf':
            $_SESSION['tab_activa'] = 'ventas'; // Recordar que vamos a la pestaña ventas
            $ventaController->generarPDFVentas();
            exit;
    }
}

// Cargar la vista
include 'views/index.php';
?>