<!DOCTYPE html>
<html>
<head>
    <title>{{$data->curso}}</title>
    <style>

    *{margin:0;}
    .fondo{
        background-image: url('{{ public_path("imagenes/Fondovocacional.png")}}');
        background-size: cover;
        opacity: 0.9;
        font-family: 'Gill Sans Extrabold', Helvetica, sans-serif;
        *{margin:2cm 2cm; padding:90px 80px 90px 85px; }
    }           
    </style>
</head>
<body class="fondo"  style="" >
    <div style="margin-top:-30px;">
        <div>
            <table style="width:100%;">
                <tr style="">
                    <td rowspan="1" style="" width="45" >
                        <div align="center" rowspan="1" >
                            <div style="width: 55px;">
                                <img src="{{ public_path('imagenes/logotiny.png')}}"  width="75">
                            </div>
                        </div>
                    </td>
                    <td align="center" style="font-size:11pt; font-weight:700;">
                        <div style="margin-top:5px;"><span style="font-size: 14pt;">UNIVERSIDAD NACIONAL DEL ALTIPLANO</span></div>
                        <div>VICERECTORADO ACADÉMICO</div>
                        <div>NIVELACIÓN INGRESANTES UNA-PUNO</div>
                    </td>
                    <td align="center" rowspan="1"> <img src="{{ public_path('imagenes/vracad.png')}}"  width="105"></td>
                </tr>
                <tr>
                    <td colspan="3" align="center">Registro de notas</td>
                </tr>
            </table>    
        </div>

        <div style="margin-top: 20px; margin-bottom:12pt">  
            <table style="width: 100%">
                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Programa de estudios:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">{{ $data->escuela }}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Semestre académico:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt;"> 2023-II {{ $data->ciclo}} </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Competencia:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt;"> {{ $data->competencia}} </span>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Curso:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt;"> {{ $data->curso}}</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Grupo:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">"{{ $data->grupo }}"</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="width:200px;">
                        <div style="text-align: left">
                            <span style="font-size:10pt;">Docente:</span>
                        </div>
                    </td>
                    <td align="left">
                        <div style="text-align: left">
                            <span style="font-size:10pt; font-weight:bold; text-transform:uppercase;">{{ $data->paterno}} {{$data->materno}}, {{ $data->nombre }}</span>
                        </div>
                    </td>
                </tr>

            </table>
        </div>


        <div style="margin-top:0px">
            <table style="width:100%; font-size:10pt; border-collapse: collapse;">
                <thead>
                    <tr style="border:solid 1px black;  height: 50px;">
                        <th style="border:solid 1px black; height: 24px;">N°</th>
                        <th style="border:solid 1px black;">DNI</th>
                        <th style="border:solid 1px black;">Apellidos</th>
                        <th style="border:solid 1px black;">Nombres</th>
                        <th style="border:solid 1px black;">Nota</th>
                        <th style="border:solid 1px black;">Condición</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($estudiantes as $index => $item)
                        <tr style="border:solid 1px black;">
                            <td style="border:solid 1px black; height: 24px;"> {{$index + 1 }}</td>
                            <td style="border:solid 1px black;">{{ $item->dni }}</td>
                            <td style="border:solid 1px black;">{{ $item->paterno }} {{ $item->materno }}</td>
                            <td style="border:solid 1px black;">{{ $item->nombres }}</td>
                            <td style="border:solid 1px black;">{{ $item->nota }}</td>
                            @if($item->nota > 10.49 && $item->nota <= 20)
                            <td style="border:solid 1px black;">Aprobado</td>
                            @else
                            <td style="border:solid 1px black;"> Desaprobado</td>
                            @endif

                        </tr>
                    @endforeach 

                </tbody>

            </table>
        </div>

    </div>
</body>
</html>