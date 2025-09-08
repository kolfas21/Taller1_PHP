# Sistema de Gestión de Empleados y Ventas

## Descripción
Aplicación PHP con PostgreSQL que implementa un sistema completo de gestión de empleados y ventas, incluyendo operaciones matemáticas avanzadas y generación de reportes en PDF.

## Características Implementadas

### 1. Gestión de Empleados
- **Operaciones de empleados:**
  - Cálculo de promedio de salarios por departamento
  - Identificación del departamento con mayor salario promedio
  - Listado de empleados que ganan por encima del promedio de su departamento

### 2. Gestión de Ventas
- **Análisis de ventas:**
  - Cálculo del total de ventas realizadas
  - Identificación del cliente que más dinero ha gastado
  - Determinación del producto más vendido

### 3. Autoload PSR-4
- Implementación completa de PSR-4 con Composer
- Estructura de namespaces organizada

### 4. Patrón MVC
- **Modelos:** `Empleado` y `Venta`
- **Controladores:** `EmpleadoController` y `VentaController`
- **Vistas:** Sistema de templates con Tailwind CSS

### 5. Operaciones Matemáticas
- **Cálculo de salario neto:** Implementa deducciones de ley colombiana (salud, pensión, retención en la fuente)
- **Interés compuesto:** Calculadora con diferentes períodos de capitalización
- **Conversión de temperatura:** Entre Celsius, Fahrenheit y Kelvin
- **Conversión de velocidad:** Entre m/s, km/h y mph

### 6. Captura de Datos
- Formularios HTML integrados para empleados y ventas
- Validación de datos en servidor
- Mensajes de éxito y error

### 7. Librerías Externas
- **dompdf/dompdf:** Generación de reportes en PDF
- **intervention/image:** Manipulación de imágenes
- **Tailwind CSS:** Framework CSS para diseño responsivo

### 8. Diseño y UX
- Interfaz moderna con Tailwind CSS
- Sistema de pestañas para navegación
- Diseño responsivo
- Experiencia de usuario optimizada

## Estructura del Proyecto

```
taller-php-postgres/
├── app/
│   ├── Config/
│   │   └── Database.php
│   ├── Controllers/
│   │   ├── EmpleadoController.php
│   │   └── VentaController.php
│   └── Models/
│       ├── Empleado.php
│       └── Venta.php
├── views/
│   └── index.php
├── vendor/
├── composer.json
├── index.php
├── init_db.php
└── README.md
```

## Instalación y Configuración

### Prerrequisitos
- PHP 7.4 o superior
- PostgreSQL
- Composer
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar base de datos:**
   - Asegúrate de que PostgreSQL esté ejecutándose
   - Usuario: `postgres`
   - Contraseña: `950430`
   - Puerto: `5432`

3. **Inicializar base de datos:**
   ```bash
   php init_db.php
   ```

4. **Acceder a la aplicación:**
   - URL: `http://localhost/laravel/taller-php-postgres/`

## Funcionalidades

### Empleados
- Agregar nuevos empleados
- Ver estadísticas por departamento
- Identificar empleados sobre el promedio
- Calculadora de salario neto

### Ventas
- Registrar nuevas ventas
- Análisis de ventas por cliente
- Identificación de productos más vendidos
- Generación de reportes PDF

### Calculadoras
- **Salario Neto:** Calcula deducciones según ley colombiana
- **Interés Compuesto:** Con diferentes períodos de capitalización
- **Conversión de Temperatura:** Entre C°, F° y K
- **Conversión de Velocidad:** Entre m/s, km/h y mph

## Base de Datos

### Tabla: empleados
- `id` (SERIAL PRIMARY KEY)
- `nombre` (VARCHAR(100))
- `salario` (DECIMAL(10,2))
- `departamento` (VARCHAR(50))
- `created_at` (TIMESTAMP)

### Tabla: ventas
- `id` (SERIAL PRIMARY KEY)
- `cliente` (VARCHAR(100))
- `producto` (VARCHAR(100))
- `cantidad` (INTEGER)
- `precio_unitario` (DECIMAL(10,2))
- `fecha_venta` (DATE)
- `created_at` (TIMESTAMP)

## Ejemplos de Uso

### Cálculo de Salario Neto
```php
$empleadoController = new EmpleadoController();
$resultado = $empleadoController->calcularSalarioNeto(3000000);
// Retorna array con deducciones y salario neto
```

### Análisis de Ventas
```php
$ventaController = new VentaController();
$datos = $ventaController->index();
// Retorna estadísticas completas de ventas
```

## Tecnologías Utilizadas
- **Backend:** PHP 7.4+
- **Base de datos:** PostgreSQL
- **Frontend:** HTML5, Tailwind CSS, JavaScript
- **Gestor de dependencias:** Composer
- **Librerías:** DomPDF, Intervention Image
- **Patrón de diseño:** MVC

## Autor
Desarrollado como proyecto académico siguiendo los requerimientos específicos del taller de PHP con PostgreSQL."# Taller1_PHP" 
