<?php

namespace Database\Seeders;

use App\Models\Estacion;
use App\Models\Residencia;
use App\Models\Zona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ZonasResidenciasSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $zonasResidencias = [
            'BIZKAIA' => ['ATXURI', 'LEBARIO', 'GERNIKA', 'ALBIA'],
            'GIPUZKOA' => ['EIBAR', 'ZUMAIA', 'DONOSTIA', 'ERRENTERIA', 'ARASO'],
        ];

        $zonas = [];

        foreach ($zonasResidencias as $zonaNombre => $residencias) {
            $zona = Zona::firstOrCreate([
                'name' => $zonaNombre,
            ]);

            $zonas[$zonaNombre] = $zona;

            foreach ($residencias as $residenciaNombre) {
                Residencia::updateOrCreate(
                    ['name' => $residenciaNombre],
                    ['zona_id' => $zona->id]
                );
            }
        }

        $csvPath = database_path('files/estaciones.csv');

        if (! File::exists($csvPath)) {
            return;
        }

        $rows = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($rows as $row) {
            $columns = str_getcsv($row, ';');

            if (count($columns) < 4) {
                continue;
            }

            $name = trim($columns[0]);
            $nemonico = trim($columns[1]);
            $pk = (float) str_replace(',', '.', trim($columns[2]));
            $zonaNombre = strtoupper(trim($columns[3]));

            if ($name === '' || $nemonico === '' || ! isset($zonas[$zonaNombre])) {
                continue;
            }

            Estacion::updateOrCreate(
                [
                    'name' =>  strtoupper($name),
                    'nemonico' => strtoupper($nemonico),
                    'zona_id' => $zonas[$zonaNombre]->id,
                ],
                [
                    'pk' => $pk,
                ]
            );
        }
    }
}
