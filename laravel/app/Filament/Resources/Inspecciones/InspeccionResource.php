<?php

namespace App\Filament\Resources\Inspecciones;

use App\Filament\Resources\Inspecciones\Pages\CreateInspeccion;
use App\Filament\Resources\Inspecciones\Pages\EditInspeccion;
use App\Filament\Resources\Inspecciones\Pages\ListInspecciones;
use App\Filament\Resources\Inspecciones\RelationManagers\ResultadosRelationManager;
use App\Filament\Resources\Inspecciones\Schemas\InspeccionForm;
use App\Filament\Resources\Inspecciones\Tables\InspeccionesTable;
use App\Models\Estacion;
use App\Models\Inspeccion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;


use UnitEnum;

class InspeccionResource extends Resource
{
    protected static ?string $model = Inspeccion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShieldCheck;



    protected static ?string $slug = 'inspecciones';

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
        $ocupadas = Inspeccion::where('type', 'periodica')->whereBetween('fecha_hora', [$currentQuarterStart, $currentQuarterEnd])
            ->pluck('estacion_id')->toArray();

        $pendingInspections = Estacion::whereNotIn('id', $ocupadas)->count();

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
        return InspeccionesTable::configure($table);
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
            'index' => ListInspecciones::route('/'),
            'create' => CreateInspeccion::route('/create'),
            'edit' => EditInspeccion::route('/{record}/edit'),
        ];
    }
}
