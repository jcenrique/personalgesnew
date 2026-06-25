<?php

namespace App\Filament\App\Resources\Inspecciones\Pages;

use App\Filament\App\Resources\Inspecciones\InspeccionesResource;
use App\Models\Estacion;
use App\Models\Inspeccion;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListInspeccion extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = InspeccionesResource::class;

     protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),


            Action::make('pendientes')
                ->label(__('Pendientes del cuatrimestre'))
                ->modalHeading(__('Inspecciones pendientes del cuatrimestre'))
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

                    // 5. Estaciones pendientes y que pertenecen a la zona del usuario logueado
                     $ocupadas = Inspeccion::whereNotIn('id', $estacionesConInspeccion)->where('type', 'periodica')->whereBetween('fecha_hora', [$inicio, $fin])

                            ->whereHas('estacion', function (Builder $query) {
                            $zonas_ids = User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();
                            $query->whereIn('zona_id', $zonas_ids);
                        })
                        ->pluck('estacion_id')->toArray();

                    $pendientes = Estacion::whereNotIn('id', $ocupadas)->whereHas('zona', function (Builder $query) {
                        $zonas_ids = User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();
                        $query->whereIn('id', $zonas_ids);
                    })->get();

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
