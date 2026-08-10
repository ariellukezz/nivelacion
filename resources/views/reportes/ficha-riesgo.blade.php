<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <title>
        Ficha de Riesgo Académico
    </title>

    <style>

        @page {
            margin: 25px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #222;
            margin: 0;
        }

        .cabecera {
            background: #1267a5;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
        }

        .cabecera h1 {
            margin: 0;
            font-size: 16px;
            text-align: center;
        }

        .cabecera p {
            text-align: center;
            margin: 5px 0 0;
            font-size: 10px;
        }

        .titulo-seccion {
            background: #eaf4fb;
            color: #12598a;
            font-weight: bold;
            padding: 8px;
            margin-top: 15px;
            margin-bottom: 5px;
            border-left: 4px solid #1267a5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #f4f6f8;
            text-align: left;
            width: 30%;
        }

        .preguntas th {
            width: 70%;
        }

        .preguntas td {
            width: 30%;
            text-align: center;
            font-weight: bold;
        }

        .pie {
            margin-top: 20px;
            border-top: 1px solid #bbb;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .barra {
            margin-bottom: 15px;
            text-align: right;
        }

        .barra button {
            border: 0;
            padding: 10px 18px;
            background: #1267a5;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        @media print {

            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
            }

        }

    </style>

</head>


<body>


@php

    $frecuencia = [
        1 => 'Nunca',
        2 => 'Casi nunca',
        3 => 'A veces',
        4 => 'Casi siempre',
        5 => 'Siempre'
    ];


    $siNo = [
        0 => 'NO',
        1 => 'SI'
    ];


    $academicos = [

        'a1' =>
            'A.1 Dificultades para asistir puntualmente',

        'a2' =>
            'A.2 Reprobación de exámenes parciales',

        'a3' =>
            'A.3 Dificultades para trabajar en grupo',

        'a4' =>
            'A.4 Dificultades para exponer',

        'a5' =>
            'A.5 Dificultades para realizar y presentar trabajos',

        'a6' =>
            'A.6 Conflictos con algún docente',

        'a7' =>
            'A.7 Habilidades y capacidades de aprender',

        'a8' =>
            'A.8 Técnicas y hábitos de estudio',

        'a9' =>
            'A.9 Vocación e identificación de la carrera',

        'a10' =>
            'A.10 Interés y motivación para estudiar'
    ];


    $personales = [

        'p1' => [
            'texto' =>
                'P.1 Problemas con la salud y estado físico',
            'tipo' => 'frecuencia'
        ],

        'p2' => [
            'texto' =>
                'P.2 Problemas con la alimentación',
            'tipo' => 'frecuencia'
        ],

        'p3' => [
            'texto' =>
                'P.3 Cuenta con una vivienda propia',
            'tipo' => 'sino'
        ],

        'p4' => [
            'texto' =>
                'P.4 Problemas con la autonomía y toma de decisiones',
            'tipo' => 'frecuencia'
        ],

        'p5' => [
            'texto' =>
                'P.5 Conflictos en las relaciones con sus compañeros',
            'tipo' => 'frecuencia'
        ],

        'p6' => [
            'texto' =>
                'P.6 Dificultades para integrarse al grupo',
            'tipo' => 'frecuencia'
        ],

        'p7' => [
            'texto' =>
                'P.7 Se siente estresado continuamente',
            'tipo' => 'frecuencia'
        ],

        'p8' => [
            'texto' =>
                'P.8 Problemas con la seguridad personal / emocional',
            'tipo' => 'frecuencia'
        ],

        'p9' => [
            'texto' =>
                'P.9 Se siente discriminado (a), marginado',
            'tipo' => 'frecuencia'
        ],

        'p10' => [
            'texto' =>
                'P.10 Problemas con sus creencias, religión',
            'tipo' => 'sino'
        ],

        'p11' => [
            'texto' =>
                'P.11 Hostigamiento sexual',
            'tipo' => 'frecuencia'
        ],

        'p12' => [
            'texto' =>
                'P.12 Limitaciones para establecer metas y aspiraciones personales (proyecto de vida)',
            'tipo' => 'frecuencia'
        ],

        'p13' => [
            'texto' =>
                'P.13 Problemas con la autoestima',
            'tipo' => 'frecuencia'
        ],
    ];


    $familiares = [

        'f1' => [
            'texto' =>
                'F.1 Conflicto en su relación con un familiar',
            'tipo' => 'frecuencia'
        ],

        'f2' => [
            'texto' =>
                'F.2 Vive solo y le afecta',
            'tipo' => 'frecuencia'
        ],

        'f3' => [
            'texto' =>
                'F.3 No cuenta con el soporte económico familiar para continuar sus estudios',
            'tipo' => 'frecuencia'
        ],

        'f4' => [
            'texto' =>
                'F.4 Tiene un familiar enfermo',
            'tipo' => 'sino'
        ],

        'f5' => [
            'texto' =>
                'F.5 Tiene familiares que dependen del estudiante',
            'tipo' => 'sino'
        ],

        'f6' => [
            'texto' =>
                'F.6 Tiene problemas de convivencia en pareja',
            'tipo' => 'frecuencia'
        ],

        'f7' => [
            'texto' =>
                'F.7 Tiene hijos y dificultades para afrontar sus responsabilidades',
            'tipo' => 'sino'
        ],

        'f8' => [
            'texto' =>
                'F.8 Ha sufrido la pérdida de un familiar cercano',
            'tipo' => 'sino'
        ],
    ];

@endphp


@if(!$modoPdf)

    <div class="barra no-print">

        <button onclick="window.print()">
            Imprimir ficha
        </button>

    </div>

@endif


<div class="cabecera">

    <h1>
        FICHA DE DATOS DE ESTUDIANTES EN RIESGO ACADÉMICO
        2026 - I
    </h1>

    <p>
        Declaración Jurada - Información confidencial
    </p>

</div>


<div class="titulo-seccion">
    DATOS DEL ESTUDIANTE
</div>


<table>

    <tr>
        <th>Fecha</th>

        <td>
            {{
                $ficha->fecha
                    ? $ficha->fecha->format('d/m/Y')
                    : ''
            }}
        </td>
    </tr>


    <tr>
        <th>Condición de matrícula</th>

        <td>

            @if($ficha->condicion_matricula == 3)

                Tercera matrícula

            @elseif($ficha->condicion_matricula == 4)

                Cuarta matrícula

            @endif

        </td>
    </tr>


    <tr>
        <th>Nombres y Apellidos</th>
        <td>
            {{ $ficha->nombres_apellidos }}
        </td>
    </tr>


    <tr>
        <th>Escuela Profesional</th>
        <td>
            {{ $ficha->escuela_profesional }}
        </td>
    </tr>


    <tr>
        <th>Ciclo académico</th>
        <td>
            {{ $ficha->ciclo_academico }}
        </td>
    </tr>


    <tr>
        <th>Código</th>
        <td>
            {{ $ficha->codigo }}
        </td>
    </tr>


    <tr>
        <th>DNI</th>
        <td>
            {{ $ficha->dni }}
        </td>
    </tr>


    <tr>
        <th>Correo electrónico</th>
        <td>
            {{ $ficha->correo_registro }}
        </td>
    </tr>


    <tr>
        <th>Correo</th>
        <td>
            {{ $ficha->correo }}
        </td>
    </tr>


    <tr>
        <th>Número de celular</th>
        <td>
            {{ $ficha->celular }}
        </td>
    </tr>


    <tr>
        <th>
            Número de celular de su Tutor
            (Padre o Madre)
        </th>

        <td>
            {{ $ficha->celular_tutor }}
        </td>
    </tr>


    <tr>
        <th>
            Número de celular de pariente
            (Hermano o familiares)
        </th>

        <td>
            {{ $ficha->celular_pariente }}
        </td>
    </tr>


    <tr>
        <th>Facebook</th>
        <td>
            {{ $ficha->facebook }}
        </td>
    </tr>


    <tr>
        <th>Lugar de procedencia</th>
        <td>
            {{ $ficha->lugar_procedencia }}
        </td>
    </tr>


    <tr>
        <th>Dirección actual de residencia</th>
        <td>
            {{ $ficha->direccion_actual }}
        </td>
    </tr>

</table>


<!-- =====================================================
     ACADÉMICOS
===================================================== -->

<div class="titulo-seccion">
    ACADÉMICOS
</div>


<table class="preguntas">

    @foreach($academicos as $campo => $pregunta)

        <tr>

            <th>
                {{ $pregunta }}
            </th>

            <td>
                {{
                    $frecuencia[
                        $ficha->$campo
                    ] ?? '-'
                }}
            </td>

        </tr>

    @endforeach

</table>


<!-- =====================================================
     PERSONALES
===================================================== -->

<div class="titulo-seccion">
    PERSONALES
</div>


<table class="preguntas">

    @foreach($personales as $campo => $pregunta)

        <tr>

            <th>
                {{ $pregunta['texto'] }}
            </th>

            <td>

                @if($pregunta['tipo'] === 'sino')

                    {{
                        $siNo[
                            $ficha->$campo
                        ] ?? '-'
                    }}

                @else

                    {{
                        $frecuencia[
                            $ficha->$campo
                        ] ?? '-'
                    }}

                @endif

            </td>

        </tr>

    @endforeach

</table>


<!-- =====================================================
     FAMILIARES
===================================================== -->

<div class="titulo-seccion">
    FAMILIARES
</div>


<table class="preguntas">

    @foreach($familiares as $campo => $pregunta)

        <tr>

            <th>
                {{ $pregunta['texto'] }}
            </th>

            <td>

                @if($pregunta['tipo'] === 'sino')

                    {{
                        $siNo[
                            $ficha->$campo
                        ] ?? '-'
                    }}

                @else

                    {{
                        $frecuencia[
                            $ficha->$campo
                        ] ?? '-'
                    }}

                @endif

            </td>

        </tr>

    @endforeach

</table>


<div class="pie">

    Ficha de Datos de Estudiantes en Riesgo Académico 2026 - II

    <br>

    Documento generado por el sistema.

</div>


@if(!$modoPdf)

<script>

    /*
    |--------------------------------------------------------------------------
    | ABRIR VENTANA DE IMPRESIÓN
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'load',
        function () {

            setTimeout(
                function () {
                    window.print();
                },
                300
            );

        }
    );

</script>

@endif


</body>

</html>