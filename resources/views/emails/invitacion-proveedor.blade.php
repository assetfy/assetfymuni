<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación a Nuestra Plataforma</title>
    <style>
        /* Estilos personalizados */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            margin: 0;
        }

        .invitacion-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dddddd;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .invitacion-header {
            background-color: #A9CFF4;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
        }

        .invitacion-header h1 {
            margin: 0;
            font-size: 1.8em;
        }

        .invitacion-content {
            margin: 20px 0;
        }

        .invitacion-button {
            background-color: #A9CFF4;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }

        .invitacion-footer {
            margin-top: 20px;
            color: #999999;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="invitacion-container">
        <div class="invitacion-header">
            <h1>Asset-fy</h1>
        </div>
        <div class="invitacion-content">
            <p>Hola {{ $proveedor['razonSocial'] }},</p>
            <p>{{ $usuario }} te ha invitado a unirte a nuestra plataforma como proveedor favorito.</p>
            <p>Para completar tu registro, haz clic en el siguiente botón:</p>
            <a href="{{ url('/forgot-password') }}" class="invitacion-button">Aceptar Invitación</a>
            <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
        </div>
        <div class="invitacion-footer">
            <p>Gracias por ser parte de nuestra comunidad.</p>
        </div>
    </div>
</body>

</html>
