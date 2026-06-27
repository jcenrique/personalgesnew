<?php

namespace Database\Seeders;

use App\Models\Estacion;
use App\Models\Zona;
use Illuminate\Database\Seeder;
use SplFileObject;

class EstacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ruta del archivo CSV
        $csvFile = database_path('files/estaciones.csv');

        // Validar que el archivo existe
        if (!file_exists($csvFile)) {
            $this->command->error("Archivo no encontrado: {$csvFile}");
            return;
        }

        // Abrimos el archivo CSV
        $file = new SplFileObject($csvFile, 'r');
        $file->setFlags(SplFileObject::READ_CSV);
        $file->setCsvControl(';'); // Delimitador es punto y coma

        foreach ($file as $line) {
            // Saltar líneas vacías
            if (empty($line[0])) {
                continue;
            }

            // Extraer datos del CSV
            $name = trim($line[0]);
            $nemonico = trim($line[1]);
            $pk = (float) str_replace(',', '.', $line[2]);
            $zonaNombre = trim($line[3]);

            // Buscar la zona por nombre
            $zona = Zona::where('name', $zonaNombre)->first();

            if (!$zona) {
                $this->command->warn("Zona no encontrada: {$zonaNombre} para estación {$name}");
                continue;
            }

            // Crear o actualizar la estación
            Estacion::updateOrCreate(
                ['nemonico' => $nemonico],
                [
                    'name' => strtoupper($name),
                    'pk' => $pk,
                    'zona_id' => $zona->id,
                ]
            );

            $this->command->info("Estación procesada: {$name}");
        }

        $this->command->info('Importación de estaciones completada.');
    }
}
