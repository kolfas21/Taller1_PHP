<?php
// Script para inicializar la base de datos

require_once 'vendor/autoload.php';
use App\Config\Database;

echo "Inicializando base de datos...\n";

try {
    // Primero intentamos conectar a la base de datos por defecto (postgres)
    $dsn = "pgsql:host=localhost;port=5432;dbname=postgres";
    $pdo = new PDO($dsn, 'postgres', '950430');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear la base de datos si no existe
    $pdo->exec("CREATE DATABASE taller_php");
    echo "Base de datos 'taller_php' creada exitosamente.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false) {
        echo "La base de datos 'taller_php' ya existe.\n";
    } else {
        echo "Error al crear la base de datos: " . $e->getMessage() . "\n";
    }
}

try {
    // Ahora conectamos a nuestra base de datos y creamos las tablas
    $database = Database::getInstance();
    $result = $database->createTables();
    
    if ($result) {
        echo "Tablas creadas exitosamente.\n";
        
        // Insertar datos de ejemplo
        $connection = $database->getConnection();
        
        // Empleados de ejemplo
        $empleados = [
            ['Juan Pérez', 2500000, 'Tecnología'],
            ['María García', 3200000, 'Tecnología'],
            ['Carlos López', 1800000, 'Ventas'],
            ['Ana Rodríguez', 2800000, 'Ventas'],
            ['Luis Martínez', 4500000, 'Tecnología'],
            ['Carmen Jiménez', 2200000, 'Marketing'],
            ['Roberto Silva', 3800000, 'Finanzas'],
            ['Laura Torres', 2600000, 'Recursos Humanos'],
            ['Diego Morales', 3100000, 'Operaciones'],
            ['Patricia Vargas', 2900000, 'Marketing']
        ];
        
        $stmt = $connection->prepare("INSERT INTO empleados (nombre, salario, departamento) VALUES (?, ?, ?)");
        foreach ($empleados as $empleado) {
            $stmt->execute($empleado);
        }
        echo "Empleados de ejemplo insertados.\n";
        
        // Ventas de ejemplo
        $ventas = [
            ['Juan Cliente', 'Laptop Dell', 2, 1200000, '2024-01-15'],
            ['María Empresa', 'Monitor Samsung', 5, 450000, '2024-01-20'],
            ['Carlos Corp', 'Teclado Mecánico', 10, 150000, '2024-02-05'],
            ['Ana Solutions', 'Mouse Logitech', 15, 80000, '2024-02-10'],
            ['Luis Tech', 'Laptop HP', 3, 1100000, '2024-02-15'],
            ['Carmen Digital', 'Webcam HD', 8, 120000, '2024-03-01'],
            ['Roberto Systems', 'Auriculares', 12, 200000, '2024-03-05'],
            ['Laura Consultores', 'Tablet Samsung', 4, 800000, '2024-03-10'],
            ['Diego Networks', 'Router WiFi', 6, 250000, '2024-03-15'],
            ['Patricia Services', 'Impresora HP', 2, 600000, '2024-03-20'],
            ['Juan Cliente', 'Monitor LG', 3, 500000, '2024-03-25'],
            ['María Empresa', 'Laptop Lenovo', 1, 1300000, '2024-04-01']
        ];
        
        $stmt = $connection->prepare("INSERT INTO ventas (cliente, producto, cantidad, precio_unitario, fecha_venta) VALUES (?, ?, ?, ?, ?)");
        foreach ($ventas as $venta) {
            $stmt->execute($venta);
        }
        echo "Ventas de ejemplo insertadas.\n";
        
        echo "\n¡Inicialización completada!\n";
        echo "Puedes acceder a la aplicación en: http://localhost/laravel/taller-php-postgres/\n";
        
    } else {
        echo "Error al crear las tablas.\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
