<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Donaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
            border-top: none;
        }
        .credentials {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
            border-radius: 4px;
        }
        .credentials strong {
            color: #28a745;
        }
        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>¡Bienvenido a Donaciones!</h1>
    </div>
    
    <div class="content">
        <p>Hola <strong>{{ $donante->nombre }}</strong>,</p>
        
        <p>Tu cuenta de donante ha sido creada exitosamente en nuestro sistema de Donaciones.</p>
        
        <div class="credentials">
            <p><strong>Tus credenciales de acceso son:</strong></p>
            <p><strong>Usuario (Email):</strong> {{ $donante->email }}</p>
            <p><strong>Contraseña temporal:</strong> {{ $password }}</p>
        </div>
        
        <div class="warning">
            <p><strong>⚠️ Importante:</strong></p>
            <p>Por tu seguridad, al iniciar sesión por primera vez se te pedirá que cambies esta contraseña temporal por una nueva de tu elección.</p>
        </div>
        
        <p>Ahora puedes:</p>
        <ul>
            <li>Realizar donaciones en dinero o especie</li>
            <li>Solicitar recolección de donaciones</li>
            <li>Ver el historial de tus donaciones</li>
            <li>Contribuir a nuestras campañas activas</li>
        </ul>
        
        <p>Gracias por formar parte de nuestra comunidad de donantes.</p>
        
        <p>Saludos cordiales,<br>
        <strong>El equipo de Donaciones</strong></p>
    </div>
    
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
    </div>
</body>
</html>




