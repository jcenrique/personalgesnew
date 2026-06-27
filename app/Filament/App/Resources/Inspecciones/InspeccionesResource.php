<?php

namespace App\Filament\App\Resources\Inspecciones;

use App\Filament\App\Resources\Inspecciones\Pages\CreateInspeccion;
use App\Filament\App\Resources\Inspecciones\Pages\EditInspeccion;
use App\Filament\App\Resources\Inspecciones\Pages\ListInspeccion;
use App\Filament\App\Resources\Inspecciones\RelationManagers\ResultadosRelationManager;
use App\Filament\App\Resources\Inspecciones\Schemas\InspeccionForm;

use App\Filament\App\Resources\Inspecciones\Tables\InspeccionTable;
use App\Models\Estacion;
use App\Models\Inspeccion;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class InspeccionesResource extends Resource
{
    protected static ?string $model = Inspeccion::class;

      protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;

    protected static ?string $recordTitleAttribute = 'nombre_estacion';

     protected static ?int $navigationSort = 21;



    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Registro Inspecciones');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Inspección');
    }
    public static function getPluralLabel(): string
    {
        return __('Inspecciones');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
     public static function getNavigationBadge(): ?string
    {
        //devolver el numero de inspecciones periodicas pendientes del cuatrimestre actual
        //las inspecciones se realizan a las estaciones y hay que comprobar que estaciones no tienen una inspeccion periodica realizada en el cuatrimestre actual, para eso hay que comprobar la fecha de la ultima inspeccion periodica realizada en cada estacion y compararla con la fecha actual, si la fecha de la ultima inspeccion periodica es anterior al inicio del cuatrimestre actual, entonces esa estacion tiene una inspeccion periodica pendiente, y hay que contar el numero de estaciones que tienen una inspeccion periodica pendiente para mostrarlo en el badge
        $currentDate = now();
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;
        $currentQuarter = ceil($currentMonth / 3);
        $currentQuarterStart = now()->setDate($currentYear, ($currentQuarter - 1) * 3 + 1, 1)->startOfDay();
        $currentQuarterEnd = now()->setDate($currentYear, $currentQuarter * 3, 1)->endOfMonth()->endOfDay();
        // Estaciones que ya tienen inspección en ese cuatrimestre
        //identificar las zonas que controla el usuario logueado
        $zonas_ids= User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();

            if(count($zonas_ids)>0){
                $ocupadas = Inspeccion::where('type', 'periodica')->whereBetween('fecha_hora', [$currentQuarterStart, $currentQuarterEnd])
                ->whereHas('estacion', function (Builder $query) use ($zonas_ids) {
                    $query->whereIn('zona_id', $zonas_ids);
                })
                ->pluck('estacion_id')->toArray();
            }
//las inspecciones pendiente son las que no estan ocupadas de la zona del usuario logueado

        $pendingInspections = Estacion::whereNotIn('id', $ocupadas)->whereHas('zona', function (Builder $query) use ($zonas_ids) {
            $query->whereIn('id', $zonas_ids);
        })->count();

        return  $pendingInspections;
    }

     public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de inspecciones totales pendientes para el cuatrimestre actual');
    }

    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }


    public static function form(Schema $schema): Schema
    {
        return InspeccionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InspeccionTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ResultadosRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInspeccion::route('/'),
            'create' => CreateInspeccion::route('/create'),
            'edit' => EditInspeccion::route('/{record}/edit'),
        ];
    }
    //reducir el numero de inspecciones cargadas en el recurso a las pertenecientes a la zona que gestiona el usuario logueado, para eso hay que comprobar la zona del usuario logueado y cargar solo las inspecciones pertenecientes a esa zona, si el usuario no tiene zona asignada, entonces se cargan todas las inspecciones
    public static function getEloquentQuery(): Builder
    {        $query = parent::getEloquentQuery();


            $zonas_ids= User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();

            if(count($zonas_ids)>0){
                $query->whereHas('estacion', function (Builder $query) use ($zonas_ids) {
                    $query->whereIn('zona_id', $zonas_ids);
                });
            }

        return $query;
    }
}
