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
            <h1>Eliminación de Empresa</h1>
        </div>
        <div class="reset-content">
            <p>Informamos que la cuenta {{ $empresa->razon_social }} ha sido eliminada exitosamente.</p>
            <p>Si deseas utilizar nuevamente nuestros servicios, recuerda que debes registrarte nuevamente. 
                Ante cualquier pregunta, no dudes en ponerte en contacto con nosotros.</p>
        </div>
        <div class="reset-footer">
            <p>Gracias por usar nuestro servicio.</p>
        </div>
    </div>
</body>

</html>