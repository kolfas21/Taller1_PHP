<?php

namespace App\Services;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailService
{
    private $mailer;
    private $fromEmail;
    private $fromName;

    public function __construct($smtpHost = null, $smtpPort = null, $smtpUser = null, $smtpPassword = null)
    {
        // Configuración por defecto (puedes cambiar estos valores)
        $smtpHost = $smtpHost ?? 'smtp.gmail.com';
        $smtpPort = $smtpPort ?? 587;
        $smtpUser = $smtpUser ?? getenv('SMTP_USER');
        $smtpPassword = $smtpPassword ?? getenv('SMTP_PASSWORD');
        
        $this->fromEmail = $smtpUser ?? 'noreply@tuempresa.com';
        $this->fromName = 'Sistema de Empleados y Ventas';

        // Crear el transporte SMTP
        if ($smtpUser && $smtpPassword) {
            $dsn = sprintf('smtp://%s:%s@%s:%d', $smtpUser, $smtpPassword, $smtpHost, $smtpPort);
        } else {
            // Usar sendmail o transport nulo para desarrollo
            $dsn = 'null://null';
        }

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new Mailer($transport);
    }

    /**
     * Enviar email de bienvenida a empleado
     */
    public function enviarBienvenidaEmpleado($empleadoEmail, $empleadoNombre, $departamento, $salario)
    {
        try {
            $email = (new Email())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to($empleadoEmail)
                ->subject('¡Bienvenido al equipo!')
                ->html($this->generarPlantillaBienvenida($empleadoNombre, $departamento, $salario));

            $this->mailer->send($email);
            return ['success' => 'Email de bienvenida enviado correctamente'];
        } catch (\Exception $e) {
            return ['error' => 'Error al enviar email: ' . $e->getMessage()];
        }
    }

    /**
     * Enviar notificación de nueva venta
     */
    public function enviarNotificacionVenta($adminEmail, $ventaData)
    {
        try {
            $email = (new Email())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to($adminEmail)
                ->subject('Nueva Venta Registrada')
                ->html($this->generarPlantillaVenta($ventaData));

            $this->mailer->send($email);
            return ['success' => 'Notificación de venta enviada correctamente'];
        } catch (\Exception $e) {
            return ['error' => 'Error al enviar notificación: ' . $e->getMessage()];
        }
    }

    /**
     * Enviar reporte mensual
     */
    public function enviarReporteMensual($adminEmail, $reporteData)
    {
        try {
            $email = (new Email())
                ->from(new Address($this->fromEmail, $this->fromName))
                ->to($adminEmail)
                ->subject('Reporte Mensual - ' . date('F Y'))
                ->html($this->generarPlantillaReporte($reporteData));

            $this->mailer->send($email);
            return ['success' => 'Reporte mensual enviado correctamente'];
        } catch (\Exception $e) {
            return ['error' => 'Error al enviar reporte: ' . $e->getMessage()];
        }
    }

    /**
     * Plantilla HTML para email de bienvenida
     */
    private function generarPlantillaBienvenida($nombre, $departamento, $salario)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #3b82f6; color: white; padding: 20px; text-align: center;'>
                <h1>¡Bienvenido al Equipo!</h1>
            </div>
            <div style='padding: 20px; background-color: #f8fafc;'>
                <h2 style='color: #1e40af;'>Hola {$nombre},</h2>
                <p>Nos complace darte la bienvenida a nuestra empresa. Aquí tienes los detalles de tu posición:</p>
                
                <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='color: #374151; margin-top: 0;'>Información del Empleado</h3>
                    <p><strong>Nombre:</strong> {$nombre}</p>
                    <p><strong>Departamento:</strong> {$departamento}</p>
                    <p><strong>Salario:</strong> $" . number_format($salario, 2) . "</p>
                </div>
                
                <p>Estamos emocionados de tenerte en nuestro equipo y esperamos que tengas mucho éxito en tu nueva posición.</p>
                
                <div style='margin-top: 30px; text-align: center;'>
                    <p style='color: #6b7280;'>¡Que tengas un excelente primer día!</p>
                </div>
            </div>
            <div style='background-color: #374151; color: white; padding: 10px; text-align: center; font-size: 12px;'>
                <p>Este es un email automático del Sistema de Empleados y Ventas</p>
            </div>
        </div>";
    }

    /**
     * Plantilla HTML para notificación de venta
     */
    private function generarPlantillaVenta($ventaData)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #059669; color: white; padding: 20px; text-align: center;'>
                <h1>Nueva Venta Registrada</h1>
            </div>
            <div style='padding: 20px; background-color: #f0fdf4;'>
                <h2 style='color: #065f46;'>Detalles de la Venta</h2>
                
                <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Producto:</strong> {$ventaData['producto']}</p>
                    <p><strong>Cantidad:</strong> {$ventaData['cantidad']}</p>
                    <p><strong>Precio Unitario:</strong> $" . number_format($ventaData['precio'], 2) . "</p>
                    <p><strong>Total:</strong> $" . number_format($ventaData['total'], 2) . "</p>
                    <p><strong>Fecha:</strong> " . date('d/m/Y H:i') . "</p>
                </div>
            </div>
        </div>";
    }

    /**
     * Plantilla HTML para reporte mensual
     */
    private function generarPlantillaReporte($reporteData)
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background-color: #7c3aed; color: white; padding: 20px; text-align: center;'>
                <h1>Reporte Mensual</h1>
                <p>" . date('F Y') . "</p>
            </div>
            <div style='padding: 20px; background-color: #faf5ff;'>
                <h2 style='color: #5b21b6;'>Resumen del Mes</h2>
                
                <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Empleados</h3>
                    <p><strong>Total de Empleados:</strong> {$reporteData['total_empleados']}</p>
                    <p><strong>Nuevos Empleados:</strong> {$reporteData['nuevos_empleados']}</p>
                </div>
                
                <div style='background-color: white; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Ventas</h3>
                    <p><strong>Total de Ventas:</strong> {$reporteData['total_ventas']}</p>
                    <p><strong>Ingresos Totales:</strong> $" . number_format($reporteData['ingresos_totales'], 2) . "</p>
                </div>
            </div>
        </div>";
    }
}
