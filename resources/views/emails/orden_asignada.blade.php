<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Orden de Trabajo Asignada</title>
    <style>
        /* Estilos personalizados */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            margin: 0;
        }

        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dddddd;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }

        .email-header {
            background-color: #A9CFF4;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
        }

        .email-header h1 {
            margin: 0;
            font-size: 1.8em;
        }

        .email-content {
            margin: 20px 0;
            text-align: left;
        }

        .email-button {
            background-color: #A9CFF4;
            color: black;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }

        .email-footer {
            margin-top: 20px;
            color: #999999;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Asset-fy</h1>
        </div>
        <div class="email-content">
            <p>Hola {{ $nombreTecnico }},</p>
            <p>Se te ha asignado una nueva orden de trabajo.</p>
            <p><strong>Número de Orden:</strong> {{ $orden->id_orden }}</p>
            <p><strong>Asignada por:</strong> {{ $asignadoPor->name }}</p>
            <p><strong>Fecha de Asignación:</strong> {{ $orden->fecha }}</p>
            <p>Para ver los detalles, haz clic en el botón a continuación:</p>
            <a href="{{ $url }}" class="email-button">Ver Ordenes</a>
        </div>
        <div class="email-footer">
            <p>Gracias por usar Asset-fy.</p>
        </div>
    </div>
</body>

</html>
