<?php

namespace App\Filament\Resources\Inspecciones\Pages;

use App\Filament\Resources\Inspecciones\InspeccionResource;
use App\Models\Estacion;
use App\Models\Inspeccion;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;

class ListInspecciones extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = InspeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),


            Action::make('pendientes')
                ->label(__('Pendientes del cuatrimestre'))
                ->modalHeading('Inspecciones pendientes del cuatrimestre')
                ->modalWidth(Width::Small)

                ->modalSubmitAction(false)
                ->modalCancelActionLabel(__('Cerrar'))
                ->modalContent(function () {
                    // 1. Obtener filtros activos
                    $filtros = $this->getTableFiltersForm()->getState();
                    $cuatri = $filtros['cuatrimestre'] ?? null;



                    // 2. Si no hay cuatrimestre → usar el actual
                    if (! $cuatri || ! str_contains($cuatri['value'], '-')) {
                        $cuatri = self::cuatrimestreActual(); // tu método existente
                    }

                    // 3. Convertir cuatrimestre en rango
                    [$year, $num] = explode('-', $cuatri['value']);

                    $ranges = [
                        1 => ['start' => "$year-01-01", 'end' => "$year-04-30"],
                        2 => ['start' => "$year-05-01", 'end' => "$year-08-31"],
                        3 => ['start' => "$year-09-01", 'end' => "$year-12-31"],
                    ];

                    $inicio = $ranges[$num]['start'];
                    $fin    = $ranges[$num]['end'];

                    // 4. Estaciones que SÍ tienen inspección en ese rango
                    $estacionesConInspeccion = Inspeccion::where('type' , 'periodica')->whereBetween('fecha_hora', [$inicio, $fin])
                        ->pluck('estacion_id')
                        ->unique();

                    // 5. Estaciones pendientes
                    $pendientes = Estacion::whereNotIn('id', $estacionesConInspeccion)->orderBy('name' , 'asc')->get();


                    // $pendientes = \App\Models\Estacion::whereDoesntHave('inspecciones', function ($q) use ($inicio, $fin) {
                    //     $q->whereBetween('fecha_hora', [$inicio, $fin]);
                    // })->get();



                    return view(
                        'filament.resources.inspecciones.pendientes',
                        ['pendientes' => $pendientes]
                    );
                }),

        ];
    }

     public static function cuatrimestreActual(): array
    {
        $year = now()->year;
        $month = now()->month;

        if ($month <= 4) {
            return ['value' => "{$year}-1"];
        } elseif ($month <= 8) {
            return ['value' => "{$year}-2"];
        } elseif ($month <= 12) {
            return ['value' => "{$year}-3"];
        }

        return ['value' => "{$year}-4"]; // si usas 4 periodos
    }
}
