<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Assetfy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/welcome.css') }}">
    @vite('resources/css/welcome.css')

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
    </style>
</head>

<body class="">
    <header>
        <div class="header-container">
            <a href="{{ url('/') }}">
                <img src="{{ asset('storage/logos/asset-fy.png') }}" alt="Logo" class="title"
                    style="height: 4rem;">
            </a>

            <img src="{{ asset('storage/fotos/9.png') }}" alt="Left Image" class="header-image">
            <img src="{{ asset('storage/fotos/8.png') }}" alt="Right Image" class="header-image">

            <div class="login-container">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}"><strong>INGRESAR</strong></a>
                @endif
            </div>
        </div>
        <div class="register-container">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="register-button"><strong>REGISTRARSE</strong></a>
            @endif
        </div>
    </header>
    <!--
        <div class="container" style="padding-top: 20px;">
            <h2 style="text-align: center; color: #0b445d"><strong>¿QUÉ PODÉS ENCONTRAR EN NUESTRA PÁGINA?</strong></h2>
            <div class="row justify-content-center">
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/registro.svg" alt="Registro">
                    <p style="font-weight: bold;">Registro</p>
                    <p style="font-size: smaller;">Registra todos tus activos en un solo lugar.</p>
                </div>
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/controles.svg" alt="Desarrollar Tu Creatividad">
                    <p style="font-weight: bold;">Controles</p>
                    <p style="font-size: smaller;">Lleva el control actual e histórico de todos tus activos, compra y vende mucho más fácil.</p>
                </div>
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/empresas.svg" alt="Arte Aplicado">
                    <p style="font-weight: bold;">Empresas</p>
                    <p style="font-size: smaller;">Puedes registrar tu empresa en el sistema, y darle un enfoque distinto al inventariado de tu activos.</p>
                </div>
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/prestadora.svg" alt="Profesores Excelentes">
                    <p style="font-weight: bold;">Prestadores</p>
                    <p style="font-size: smaller;">Al registrarte como trabajador particular o empresa prestadora van a poder verificar su integridad como profesionales, no te quedes atras.</p>
                </div>
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/transferencia.svg" alt="Ampliar Tu Portfolio">
                    <p style="font-weight: bold;">Vinculación de activos</p>
                    <p style="font-size: smaller;">Podrás vincular dos activos o más que se hallen asociados.</p>
                </div>
                <div class="col-6 col-sm-4 col-md-2 feature-box">
                    <img src="../storage/fotos/venta.svg" alt="Networking">
                    <p style="font-weight: bold;">Transferencia de activos</p>
                    <p style="font-size: smaller;">Podrás vender tus activos y transferirle la custodia a otro usuario.</p>
                </div>
            </div>
        </div>
        -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
    <script>
        AOS.init();
    </script>
</body>

</html>
