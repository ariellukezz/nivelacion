<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Sorteados</title>
    <style>
        body {
            font-family: Helvetica, sans-serif;
            font-size: 12px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #ccc;
        }

        td.firma {
            height: 40px;       /* Aumenta la altura de la celda de firma */
            width: 120px;       /* Aumenta el ancho de la celda de firma */
        }

        h2 {
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h2><strong>LISTA DE SORTEADOS SOUVENIR INDUCCIÓN 2025-II</strong></h2>

    <table>
        <thead>
            <tr>
                <th>N°</th>
                <th>NOMBRES</th>
                <th>PROGRAMA</th>
                <th style="width: 180px;">FIRMA</th> <!-- También se puede definir aquí -->
            </tr>
        </thead>
        <tbody>
            @foreach($ganadores as $ganador)
            <tr>
                <td>{{ $ganador->orden_sorteo }}</td>
                <td>{{ strtoupper("$ganador->paterno $ganador->materno, $ganador->nombres") }}</td>
                <td>{{ strtoupper($ganador->programa) }}</td>
                <td class="firma"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
