<?php
    use Carbon\Carbon;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            margin: 40px;
            font-family: Arial, sans-serif;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
            font-size: 12px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        thead {
            background-color: #f0f0f0;
        }
        thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #000;
            font-size: 11px;
        }
        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
        }
        .total {
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>{{ __('Reporte de asistencia') }}</p>
    </div>

    <div class="info-section">
        <p><span class="label">{{ __('Curso:') }}</span> {{ $course->name }}</p>
        <p><span class="label">{{ __('Descripción:') }}</span> {{ $course->description }}</p>
        <p><span class="label">{{ __('Horas:') }}</span> {{ $course->duration_hours }}</p>
        <p><span class="label">{{ __('Fecha de generación:') }}</span> {{ Carbon::now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">#</th>
                <th style="width: 30%">{{ __('Nombre') }}</th>
                <th style="width: 35%">{{ __('Email') }}</th>
                <th style="width: 30%">{{ __('Roles') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendees as $key => $attendee)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $attendee->name }}</td>
                    <td>{{ $attendee->email }}</td>
                    <td>{{ ucwords (str_replace('_' , ' ' , $attendee->roles->pluck('name')->sort()->join(', '))) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">
                        {{ __('No hay asistentes confirmados') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        <p>{{ __('Total de asistentes: ') }} <strong>{{ $attendees->count() }}</strong></p>
    </div>

    <div class="footer">
        <p>{{ __('Este documento ha sido generado automáticamente') }}</p>
    </div>
</body>
</html>
