<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
            text-align: left;
        }

        .no-border td {
            border: none;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .category {
            font-weight: bold;
            background: #eaeaea;
        }

        .elementos{

            font-weight: bold;
            background: #838282;

        }
        .page-break {
            page-break-after: always;
        }

        @page {
            margin: 30px 30px 30px 30px;
        }

        #footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>


    {{-- ============================
    HOJA 1 – EUSKERA
============================= --}}

    <table class="" style="width: 100%; margin-bottom: 10px;border: 2px solid #000; border-collapse: collapse;">
        <tr>
            <td style="width: 120px;text-align: center; vertical-align: middle;">
                <img src="data:image/jpeg;base64,{{ $logo }}" style="width: 90px;">
            </td>
            <td style="text-align: center; font-size: 16px; font-weight: bold;vertical-align: middle;">
                ZIRKULAZIO PROZEDUREN IKUSKAPEN ETA<br>
                ELEMENTUEN FUNTZIONAMENDUAREN EGIAZTAPEN FITXA
            </td>
        </tr>
    </table>


    <table class="">
        <tr>
            <td class="category">EGUNA:</td>
            <td>{{  $fecha_eu }}</td>
            <td class="category">ORDUA:</td>
            <td>{{ $inspeccion->fecha_hora->translatedFormat('H:i') }}</td>
        </tr>

        <tr>
            <td colspan="2" class="category">GELTOKIA:</td>
            <td colspan="2">{{ $inspeccion->estacion->name }}</td>
        </tr>
        <tr>
            <td colspan="2" class="category" style="vertical-align: middle;">AGENTEA:</td>
            <td colspan="2">
                 <div>
                    {{ $inspeccion->user2->name }}
                </div>
                <div style="font-size: 10px; color: #555;">
                    {{ $roles }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="category">IKUSKATZAILEA:</td>
            <td colspan="2">{{ $inspeccion->user1->name }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th style="width: 40%; text-align: center" class="elementos">ELEMENTUAK</th>
            <th style="width: 6%; text-align: center" class="elementos">ONDO</th>
            <th style="width: 6%; text-align: center" class="elementos">GAIZKI</th>
            <th style="width: 48%; text-align: center" class="elementos">OHARRAK</th>
        </tr>

        @foreach ($inspeccion->resultados->groupBy('elemento.categoria.nombre_eu') as $categoria => $items)
            <tr>
                <td class="category" colspan="4">{{ $loop->index + 1 }}. {{ $categoria }}</td>

            </tr>

            @foreach ($items as $resultado)
                <tr>
                    <td>{{ $resultado->elemento->nombre_eu }}</td>
                    <td style="text-align: center">{{ $resultado->resultado ? 'X' : '' }}</td>
                    <td style="text-align: center">{{ !$resultado->resultado ? 'X' : '' }}</td>
                    <td>{{ $resultado->observacion }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>

    <div id="footer">
        {{ $formato_id }}
    </div>
    <div class="page-break"></div>

    {{-- ============================
    HOJA 2 – EUSKERA (OBSERVACIONES)
============================= --}}
    <table>
        <tr >
            <th class="elementos" >OHARRAK:</th>
        </tr>
        <tr>
            <td style="height: 90%;">{!! $inspeccion->observaciones !!}</td>
        </tr>
    </table>

    <div id="footer">
        {{ $formato_id }}
    </div>

    <div class="page-break"></div>

    {{-- ============================
    HOJA 3 – CASTELLANO
============================= --}}

    <table class="" style="width: 100%; margin-bottom: 10px; border: 2px solid #000; border-collapse: collapse;">
        <tr>
            <td style="width: 120px;text-align: center; vertical-align: middle;">
                <img src="data:image/jpeg;base64,{{ $logo }}" style="width: 90px;">
            </td>
            <td style="text-align: center; font-size: 16px; font-weight: bold;vertical-align: middle;">
                FICHA INSPECCIÓN DE PROCEDIMIENTOS DE CIRCULACIÓN Y<br>
                COMPROBACIÓN DE FUNCIONAMIENTO DE ELEMENTOS
            </td>
        </tr>
    </table>

    <table class="">
        <tr>
            <td class="category">FECHA:</td>
            <td>{{ $fecha_es  }}</td>
            <td class="category">HORA:</td>
            <td>{{ $inspeccion->fecha_hora->translatedFormat('H:i') }}</td>
        </tr>

        <tr>
            <td colspan="2" class="category">ESTACIÓN:</td>
            <td colspan="2">{{ $inspeccion->estacion->name }}</td>

        </tr>

        <tr>
            <td colspan="2" class="category" style="vertical-align: middle;">AGENTE:</td>
            <td colspan="2">
                <div>
                    {{ $inspeccion->user2->name }}
                </div>
                <div style="font-size: 10px; color: #555;">
                    {{ $roles }}
                </div>
            </td>

        </tr>
        <tr>
            <td colspan="2" class="category">INSPECTOR:</td>
            <td colspan="2">{{ $inspeccion->user1->name }}</td>

        </tr>
    </table>

    <table>
        <tr>
            <th style="width: 40%; text-align: center" class="elementos">ELEMENTOS</th>
            <th style="width: 6%; text-align: center" class="elementos">BIEN</th>
            <th style="width: 6%; text-align: center" class="elementos">MAL</th>
            <th style="width: 48%; text-align: center" class="elementos">OBSERVACIONES</th>
        </tr>

        @foreach ($inspeccion->resultados->groupBy('elemento.categoria.nombre_es') as $categoria => $items)
            <tr>
                <td class="category" colspan="4">{{ $loop->index + 1 }}. {{ $categoria }}</td>

            </tr>

            @foreach ($items as $resultado)
                <tr>
                    <td>{{ $resultado->elemento->nombre_es }}</td>
                    <td style="text-align: center">{{ $resultado->resultado ? 'X' : '' }}</td>
                    <td style="text-align: center">{{ !$resultado->resultado ? 'X' : '' }}</td>
                    <td>{{ $resultado->observacion }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>

    <div id="footer">
        {{ $formato_id }}
    </div>

    <div class="page-break"></div>

    {{-- ============================
    HOJA 4 – CASTELLANO (OBSERVACIONES)
============================= --}}
    <table>
        <tr>
            <th class="elementos">OBSERVACIONES:</th>
        </tr>
        <tr>
            <td style="height: 90%;">{!! $inspeccion->observaciones !!}</td>
        </tr>
    </table>

    <div id="footer">
        {{ $formato_id }}
    </div>

</body>

</html>
