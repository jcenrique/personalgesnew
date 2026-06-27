<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>ZIRKULAZIO IKUSKAPEN BEREZIAREN TXOSTENA</title>

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

        .section-title {
            background: #e6e6e6;
            font-weight: bold;
            padding: 6px;
            text-transform: uppercase;
        }

        .spacer {
            height: 20px;
        }

        .cuestiones {
            height: 12%;
        }

        .anomalias {
            height: 25%;
        }

        .spacer-sign {
            height: 100px;
            border-width: 2px;
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

        #evaluacion {
             font-size: 12px;
             font-weight: bold;
             padding-left: 20px;
             padding-top: 10px;
            color: #0733fc;
        }
        }
    </style>
</head>

<body>

    <table class="" style="width: 100%; margin-bottom: 10px;border: 2px solid #000; border-collapse: collapse;">
        <tr>
            <td style="width: 120px;text-align: center; vertical-align: middle;">
                <img src="data:image/jpeg;base64,{{ $logo }}" style="width: 90px;">
            </td>
            <td style="text-align: center; font-size: 16px; font-weight: bold;vertical-align: middle;">
                ZIRKULAZIO IKUSKAPEN BEREZIAREN TXOSTENA<br>INFORME INSPECCIÓN ESPECIAL DE CIRCULACIÓN
            </td>
        </tr>
    </table>



    {{-- 1. IDENTIFICACIÓN --}}
    <table>
        <tr>
            <td colspan="2" class="section-title">1. IDENTIFIKAZIOA / IDENTIFICACIÓN</td>
        </tr>

        <tr>
            <td style="width: 35%; ">DATA / FECHA:</td>
            <td>{{ $fecha_es ?? '' }}</td>
        </tr>

        <tr>
            <td style="width: 35%; ">BISITAREN GAIA / TEMA VISITA:</td>
            <td>{{ $inspeccion->tema }}</td>
        </tr>

        <tr>
            <td style="width: 35%; ">BISITAREN PUNTUA / PUNTO VISITA:</td>
            <td>{{ $inspeccion->estacion->name ?? '' }}</td>
        </tr>

        {{-- 2. OBJETO DE LA INSPECCIÓN --}}
        <tr>
            <td colspan="2" class="section-title">
                2. IKUSKAPENAREN XEDE DIREN GAIAK / CUESTIONES OBJETO DE LA INSPECCIÓN
            </td>
        </tr>


        <tr>
            <td colspan="2" class="cuestiones">{!! $inspeccion->cuestiones !!}</td>
        </tr>


        {{-- 3. RESULTADO --}}
        <tr>
            <td colspan="2" class="section-title">
                3. IKUSKAPENAREN EMAITZA (hautemandako arazoak) /
                RESULTADO DE LA INSPECCIÓN (anomalías observadas)
            </td>
        </tr>


        <tr>
            <td colspan="2" class="anomalias">{!! $inspeccion->observaciones !!}</td>
        </tr>


        {{-- 4. EVALUACIÓN --}}
        <tr>
            <td colspan="2" class="section-title">4. EBALUAZIOA / EVALUACIÓN</td>
        </tr>

        <tr>
            <td colspan="2">
                Konpontzeko ekintzak (ez, bai) / Acciones correctivas (no, sí):<br>
                <div id="evaluacion">
                    {{ $inspeccion->actions?'SI':'NO' }}

                </div>
                <br>
            </td>
        </tr>

        <tr>
            <td colspan="2" >
                Arazoak jakinarazi diren data / Fecha comunicación de anomalías:<BR>
                <div id="evaluacion">
                    {{ $inspeccion->fecha_comunicacion?->translatedFormat('d F Y') ?? ''}}<br><br>
                </div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="width: 50%" class="spacer-sign">
                IKUSKATZAILEA<br>
                INSPECTOR:<br>
                <div id="evaluacion">
                    {{ $inspeccion->user1->name }}
                </div>


            </td>
            <td style="width: 50%" class="spacer-sign">
                AGINTE POSTUKO ARDURADUNAREN ON IKUSIA<br>
                VISTO BUENO RESPONSABLE P.M.:

            </td>
        </tr>
    </table>


    <br><br>
    <div id="footer">
        {{ $formato_id }}
    </div>

</body>

</html>
