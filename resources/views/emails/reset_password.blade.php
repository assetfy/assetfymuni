<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .reset-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #dddddd;
            border-radius: 5px;
            text-align: center;
        }

        .reset-header {
            background-color: #A9CFF4;
            color: white;
            padding: 10px 0;
            border-radius: 5px 5px 0 0;
        }

        .reset-header h1 {
            text-align: center;
            color: white;
            padding: 10px 0;
            font-size: 2em;
        }

        .reset-content p,
        .reset-footer p {
            margin: 15px 0;
            text-align: center;
        }

        .reset-button {
            background-color: #A9CFF4;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }

        .reset-footer {
            text-align: center;
            color: #999999;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1>Modificación de Contraseña</h1>
        </div>
        <div class="reset-content">
            <p>Hola, hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el botón de abajo para restablecer tu contraseña. Este enlace expirará en 24 horas.</p>
            <a href="{{ $resetUrl }}" class="reset-button">Restablecer Contraseña</a>
            <p>Si no solicitaste esto, por favor ignora este correo. Tu contraseña no cambiará.</p>
            <p>La empresa se compromete con tu privacidad. Aprende más sobre nuestro aviso de privacidad y términos de servicio.</p>
        </div>
        <div class="reset-footer">
            <p>Saludos, Equipo de Soporte</p>
        </div>
    </div>
</body>

</html>