# Sistema de Gestión de Empleados y Ventas

## Descripción
Aplicación PHP con PostgreSQL que implementa un sistema completo de gestión de empleados y ventas, incluyendo operaciones matemáticas avanzadas, generación de reportes en PDF, **envío de correos electrónicos** y **manipulación de imágenes**.

## Características Implementadas

### 1. Gestión de Empleados
- **Operaciones de empleados:**
  - Cálculo de promedio de salarios por departamento
  - Identificación del departamento con mayor salario promedio
  - Listado de empleados que ganan por encima del promedio de su departamento
  - **Subida y procesamiento de fotos de empleados**
  - **Envío automático de emails de bienvenida**

### 2. Gestión de Ventas
- **Análisis de ventas:**
  - Cálculo del total de ventas realizadas
  - Identificación del cliente que más dinero ha gastado
  - Determinación del producto más vendido
  - **Notificaciones de ventas por email**

### 3. Autoload PSR-4
- Implementación completa de PSR-4 con Composer
- Estructura de namespaces organizada

### 4. Patrón MVC
- **Modelos:** `Empleado` y `Venta`
- **Controladores:** `EmpleadoController` y `VentaController`
- **Servicios:** `MailService`, `ImageService`, `SimpleImageService`
- **Vistas:** Sistema de templates con Tailwind CSS

### 5. Operaciones Matemáticas
- **Cálculo de salario neto:** Implementa deducciones de ley colombiana 2025 (salud, pensión, retención en la fuente con UVT)
- **Interés compuesto:** Calculadora con diferentes períodos de capitalización
- **Conversión de temperatura:** Entre Celsius, Fahrenheit y Kelvin
- **Conversión de velocidad:** Entre m/s, km/h y mph

### 6. Sistema de Correos (Symfony Mailer)
- **Emails automáticos de bienvenida** para nuevos empleados
- **Notificaciones de ventas** para administradores
- **Reportes mensuales** por email
- **Plantillas HTML responsivas**
- **Soporte para múltiples proveedores SMTP**

## Nuevas Características 2025

### Sistema de Correo Electrónico (Symfony Mailer)
- **Configuración flexible**: Soporte para múltiples proveedores SMTP (Gmail, SendGrid, MailHog)
- **Plantillas HTML**: Correos con diseño profesional utilizando Tailwind CSS
- **Tipos de correo**:
  - Email de bienvenida para nuevos empleados
  - Notificaciones de ventas realizadas
  - Reportes mensuales automatizados
- **Configuración opcional**: El sistema funciona sin configuración de email
- **Configuración en .env**:
  ```env
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=tu_email@gmail.com
  MAIL_PASSWORD=tu_password_de_aplicacion
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=tu_email@gmail.com
  MAIL_FROM_NAME="Sistema UVT"
  ```

### Procesamiento de Imágenes (Intervention Image v3)
- **Redimensionamiento automático**: Las fotos se ajustan a 300x300 píxeles
- **Optimización**: Compresión inteligente para reducir tamaño de archivo
- **Validación robusta**: Verificación de tipos y tamaños de archivo
- **Sistema de respaldo**: SimpleImageService como alternativa ligera
- **Soporte de formatos**: JPG, PNG, GIF, WebP
- **Límites**: Máximo 2MB por imagen para compatibilidad

### Interfaz Mejorada
- **Campos de imagen**: Upload y preview de fotos para empleados
- **Campos de email**: Validación y envío automático de notificaciones
- **Diseño responsive**: Tailwind CSS para mejor experiencia móvil
- **Feedback visual**: Mensajes de éxito y error mejorados

### Cálculos UVT 2025
- **Valor actualizado**: $47,065 COP para 2025
- **Cálculo automático**: Conversión directa de salarios a UVT
- **Visualización clara**: Tanto en pesos como en UVT

## Arquitectura del Sistema

### Estructura de Archivos
```
├── app/
│   ├── Config/Database.php          # Configuración de base de datos
│   ├── Controllers/                 # Controladores MVC
│   │   ├── EmpleadoController.php
│   │   └── VentaController.php
│   ├── Models/                      # Modelos de datos
│   │   ├── Empleado.php
│   │   └── Venta.php
│   └── Services/                    # Servicios de negocio
│       ├── MailService.php          # Sistema de correo
│       ├── ImageService.php         # Procesamiento de imágenes
│       └── SimpleImageService.php   # Servicio básico de imágenes
├── uploads/                         # Archivos subidos
├── views/index.php                  # Vista principal
├── .env.example                     # Plantilla de configuración
└── composer.json                    # Dependencias PHP
```

### Servicios Implementados

#### MailService
- Envío de emails con plantillas HTML
- Soporte para múltiples proveedores SMTP
- Manejo de errores y fallbacks
- Registro de logs detallado

#### ImageService
- Procesamiento avanzado con Intervention Image v3
- Redimensionamiento y optimización automática
- Validación de archivos y formatos
- Sistema de respaldo con SimpleImageService

### Base de Datos
- **PostgreSQL** como motor principal
- Tablas actualizadas con campos de email y foto
- Schema compatible con versiones anteriores
- Soporte para datos multimedia

### 8. Captura de Datos
- Formularios HTML integrados para empleados y ventas
- **Campo de email** para empleados
- **Campo de foto** para empleados
- Validación de datos en servidor
- Mensajes de éxito y error

### 9. Librerías Externas
- **dompdf/dompdf:** Generación de reportes en PDF
- **intervention/image:** Manipulación y procesamiento de imágenes
- **symfony/mailer:** Sistema completo de correo electrónico
- **Tailwind CSS:** Framework CSS para diseño responsivo

### 10. Diseño y UX
- Interfaz moderna con Tailwind CSS
- Sistema de pestañas con **persistencia de estado**
- **Navegación mejorada** sin recargas de página
- **Diseño compacto** optimizado para pantallas
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
│   ├── Models/
│   │   ├── Empleado.php
│   │   └── Venta.php
│   └── Services/
│       ├── MailService.php
│       ├── ImageService.php
│       └── SimpleImageService.php
├── uploads/
│   ├── empleados/
│   ├── productos/
│   ├── reportes/
│   └── thumbnails/
├── views/
│   └── index.php
├── vendor/
├── .env.example
├── composer.json
├── index.php
├── init_db.php
├── INTEGRACION.md
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
   - Usuario: `********`
   - Contraseña: `********`
   - Puerto: `5432`

3. **Configurar emails (opcional):**
   - Copia `.env.example` a `.env`
   - Configura las variables SMTP:
   ```bash
   SMTP_USER=tu_email@gmail.com
   SMTP_PASSWORD=tu_password_de_aplicacion
   ADMIN_EMAIL=admin@tuempresa.com
   ```

4. **Inicializar base de datos:**
   ```bash
   php init_db.php
   ```

5. **Acceder a la aplicación:**
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
- `email` (VARCHAR(100)) - **NUEVO**
- `foto` (VARCHAR(255)) - **NUEVO**
- `created_at` (TIMESTAMP)

### Tabla: ventas
- `id` (SERIAL PRIMARY KEY)
- `cliente` (VARCHAR(100))
- `producto` (VARCHAR(100))
- `cantidad` (INTEGER)
- `precio_unitario` (DECIMAL(10,2))
- `fecha_venta` (DATE)
- `imagen_producto` (VARCHAR(255)) - **NUEVO**
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
- **Librerías:** 
  - DomPDF (Generación de PDFs)
  - Intervention Image v3 (Procesamiento de imágenes)
  - Symfony Mailer v6 (Sistema de correos)
- **Patrón de diseño:** MVC con Servicios

## Nuevas Funcionalidades 2025
- ✅ **Sistema de correo electrónico** con Symfony Mailer
- ✅ **Procesamiento de imágenes** con Intervention Image v3
- ✅ **Persistencia de pestañas** entre formularios
- ✅ **Cálculo actualizado** de retención en la fuente (UVT 2025)
- ✅ **Interfaz compacta** sin scroll horizontal
- ✅ **Manejo robusto de errores** con fallbacks

## Documentación Adicional
- Ver `INTEGRACION.md` para detalles de las nuevas integraciones
- Ver `.env.example` para configuración de correo electrónico

## Autor
Desarrollado como proyecto académico siguiendo los requerimientos específicos del taller de PHP con PostgreSQL."# Taller1_PHP" 
