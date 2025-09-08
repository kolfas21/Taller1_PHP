<?php
require_once 'vendor/autoload.php';

use App\Services\MailService;

// Prueba de configuración de email
echo "<h2>🧪 Prueba de Configuración de Email</h2>";

try {
    // Crear servicio de email
    $mailService = new MailService();
    
    echo "<p>✅ <strong>MailService iniciado correctamente</strong></p>";
    
    // Mostrar configuración cargada
    echo "<h3>📧 Configuración Detectada:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . ($_ENV['MAIL_HOST'] ?? 'No configurado') . "</li>";
    echo "<li><strong>Puerto:</strong> " . ($_ENV['MAIL_PORT'] ?? 'No configurado') . "</li>";
    echo "<li><strong>Usuario:</strong> " . ($_ENV['MAIL_USERNAME'] ?? 'No configurado') . "</li>";
    echo "<li><strong>Contraseña:</strong> " . (isset($_ENV['MAIL_PASSWORD']) ? str_repeat('*', strlen($_ENV['MAIL_PASSWORD'])) : 'No configurada') . "</li>";
    echo "<li><strong>Desde Email:</strong> " . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'No configurado') . "</li>";
    echo "<li><strong>Desde Nombre:</strong> " . ($_ENV['MAIL_FROM_NAME'] ?? 'No configurado') . "</li>";
    echo "</ul>";
    
    // Prueba de envío de email
    echo "<h3>📤 Enviando email de prueba...</h3>";
    
    $resultado = $mailService->enviarBienvenidaEmpleado(
        'sling.lite@gmail.com', // Tu mismo email para la prueba
        'Usuario de Prueba',
        'Departamento de Sistemas',
        5000000
    );
    
    if ($resultado) {
        echo "<p style='color: green;'>✅ <strong>¡Email enviado exitosamente!</strong></p>";
        echo "<p>Revisa tu bandeja de entrada en <strong>sling.lite@gmail.com</strong></p>";
    } else {
        echo "<p style='color: orange;'>⚠️ <strong>Email procesado, pero revisa la configuración</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Sugerencias:</strong></p>";
    echo "<ul>";
    echo "<li>Verifica que la contraseña de aplicación esté correcta</li>";
    echo "<li>Asegúrate que la verificación en 2 pasos esté activada en Gmail</li>";
    echo "<li>Revisa que no haya espacios extras en la configuración</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Volver a la aplicación principal</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
}

h2, h3 {
    color: #333;
}

ul {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

li {
    margin: 5px 0;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
