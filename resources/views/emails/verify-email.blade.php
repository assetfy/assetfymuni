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
        }

        .reset-header {
            background-color: #A9CFF4;
            color: white;
            padding: 10px 0;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .reset-content {
            margin: 20px 0;
            text-align: center;
        }

        .reset-button {
            background-color: #A9CFF4;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
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
            <h1>Verificación de Correo Electrónico</h1>
        </div>
        <div class="reset-content">
            <p>Hola,</p>
            <p>Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.</p>
            <a href="{{ $verificationUrl }}" class="reset-button">Verificar Correo Electrónico</a>
            <p>Si no creaste una cuenta, no es necesario que realices ninguna otra acción.</p>
        </div>
        <div class="reset-footer">
            <p>Saludos, Equipo de Soporte</p>
        </div>
    </div>
</body>

</html>