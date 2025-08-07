<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Documentos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
            margin: 1cm; /* Márgenes más pequeños para maximizar el área de impresión */
        }
        .fondo {
            background-image: url('{{ public_path("imagenes/Fondovocacional.png") }}');
            background-size: cover;
            opacity: 0.9;
            width: 100%;
            min-height: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: solid 1px black;
            padding: 1px;
            font-size: 9pt; /* Reducimos el tamaño de fuente para evitar desbordamiento */
        }
        .header-table {
            margin-bottom: 15px;
        }
        .header-table img {
            max-width: 80px; /* Ajustamos el tamaño de las imágenes */
            vertical-align: middle;
        }
        .footer {
            position: fixed;
            bottom: 1cm;
            width: 100%;
            text-align: center;
            font-size: 10pt;
        }
    </style>
</head>
<body class="fondo">
    <div>
        <!-- Encabezado -->
        <div class="header-table">
            <table>
                <tr>
                    <td width="50">
                        <div align="center">
                            <img src="{{ public_path('imagenes/logotiny.png') }}" width="60">
                        </div>
                    </td>
                    <td align="center" style="font-size: 10pt; font-weight: bold;">
                        <div><span style="font-size: 12pt;">UNIVERSIDAD NACIONAL DEL ALTIPLANO</span></div>
                        <div>VICERRECTORADO ACADÉMICO</div>
                        <div>REPORTE DE DOCUMENTOS</div>
                    </td>
                    <td width="80" align="center">
                        <img src="{{ public_path('imagenes/vracad.png') }}" width="80">
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center" style="font-size: 10pt; font-weight: bold;">
                        ESTADO DE DOCUMENTOS POR ESCUELA Y PERIODO
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de resultados -->
        <div>
            <table>
                <thead>
                    <tr>
                        <th>Escuela</th>
                        <th>Periodo</th>
                        <th>Tipo Doc</th>
                        <th>Estado</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reporte as $item)
                        <tr>
                            <td>{{ $item->escuela }}</td>
                            <td>{{ $item->periodo }}</td>
                            <td>{{ $item->tipo }}</td>
                            <td>{{ $item->estado }}</td>
                            <td>{{ $item->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            Puno, {{ strftime("%d de %B del %Y") }}
        </div>
    </div>
</body>
</html>
