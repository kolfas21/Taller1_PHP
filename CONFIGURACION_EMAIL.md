# 📧 Configuración de Email - Guía Rápida

## Para Gmail (Recomendado)

### Paso 1: Activar Verificación en 2 Pasos
1. Ve a tu cuenta de Google → Seguridad
2. Activa "Verificación en 2 pasos"

### Paso 2: Generar Contraseña de Aplicación
1. En Seguridad → Contraseñas de aplicaciones
2. Selecciona "Correo" 
3. Copia la contraseña de 16 caracteres (ejemplo: `abcd efgh ijkl mnop`)

### Paso 3: Configurar .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@gmail.com
MAIL_PASSWORD=abcd efgh ijkl mnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@gmail.com
MAIL_FROM_NAME="Sistema UVT"
```

## Para Outlook/Hotmail

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@hotmail.com
MAIL_PASSWORD=tu_contraseña_normal
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@hotmail.com
MAIL_FROM_NAME="Sistema UVT"
```

## Para Yahoo

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=tucorreo@yahoo.com
MAIL_PASSWORD=contraseña_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tucorreo@yahoo.com
MAIL_FROM_NAME="Sistema UVT"
```

## 🚀 ¿Qué hace la app automáticamente?

### ✅ **TODO automatizado:**
- ✅ Conecta al servidor SMTP
- ✅ Autentica con las credenciales
- ✅ Envía emails con plantillas HTML bonitas
- ✅ Maneja errores automáticamente
- ✅ Si falla el email, la app sigue funcionando normal

### 📨 **Emails que se envían solos:**
1. **Bienvenida** cuando agregas un empleado
2. **Notificación** cuando registras una venta
3. **Reportes** (si lo activas)

## ⚡ **Resumen: Solo necesitas 2 cosas**
1. Tu email personal
2. Contraseña de aplicación (Gmail) o contraseña normal (otros)

**¡La app se encarga de todo lo demás!** 🎉

## 🔒 **Seguridad**
- Nunca pongas tu contraseña real en Gmail
- Usa siempre contraseña de aplicación
- El archivo .env nunca se sube a GitHub (está en .gitignore)
