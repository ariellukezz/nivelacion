<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <title inertia>{{ config('app.name', 'Nivelacion') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="shortcut icon" href="{{ asset('favicon.ico')}}">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
<!--
|--------------------------------------------------------------------------
| AUTORÍA DEL SISTEMA
|--------------------------------------------------------------------------
|
| Universidad Nacional del Altiplano de Puno
| Vicerrectorado Académico
|
| Sistema desarrollado, diseñado, programado e implementado originalmente por:
|
| - Ing. Brayan Darwin Huanca Huayta
| - Ing. Michael Newton Cutipa Santi
|
| Año de creación / implementación: 2026
|
| La autoría original corresponde a los desarrolladores antes mencionados.
| Mantener esta referencia de autoría en futuras modificaciones,
| actualizaciones o labores de mantenimiento del sistema.
|
|--------------------------------------------------------------------------
-->
        @inertia
    </body>
</html>
