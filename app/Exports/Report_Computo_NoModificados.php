<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Report_Computo_NoModificados implements FromArray, ShouldAutoSize, WithColumnWidths, WithStyles
{



    protected $data;
    protected $year;
    protected $count;


    public function __construct($data, $year, $rows)
    {

        $this->data = $data;
        $this->year = $year;
        $this->count = $rows;
    }


    public function array(): array
    {


        // 🔴 Cabecera personalizada + datos
        return array_merge(
            [
                ['Informe de cómputos no modificados'],
                ['Año', $this->year],
                ['Total registros para importar', $this->count],
                ['Total registros no importados', count($this->data)],
                ['Fecha importación', now()->translatedFormat('Y-m-d')],
                [''], // línea vacía
                ['Registros no importados'], // línea vacía
                ['Código agente', 'Nombre', 'Horas disfrutadas'],

            ],
            $this->data
        );
    }


    public function columnWidths(): array
    {
        return [
            'A' => 27,

        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 16]],
            7    => ['font' => ['bold' => true, 'size' => 16]],
            8    => ['font' => ['bold' => true, 'size' => 12]],
            'A2:A5' => ['font' => ['bold' => true]],
            'B2:B5' => ['alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]],
            'A9:C100' => ['alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]],

        ];
    }
}
