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
use Illuminate\Database\Eloquent\Builder;


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
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        $zonaIds = $user->zonas()->pluck('zonas.id');

        if ($zonaIds->isEmpty()) {
            return '0';
        }

        // El cuatrimestre se divide en periodos de 4 meses: Ene-Abr, May-Ago, Sep-Dic.
        $currentDate = now();
        $currentMonth = $currentDate->month;
        $currentYear = $currentDate->year;
        $currentCuatrimestre = (int) ceil($currentMonth / 4);
        $currentCuatrimestreStart = now()->setDate($currentYear, ($currentCuatrimestre - 1) * 4 + 1, 1)->startOfDay();
        $currentCuatrimestreEnd = now()->setDate($currentYear, $currentCuatrimestre * 4, 1)->endOfMonth()->endOfDay();

        $ocupadas = Inspeccion::query()
            ->where('type', 'periodica')
            ->whereBetween('fecha_hora', [$currentCuatrimestreStart, $currentCuatrimestreEnd])
            ->whereHas('estacion', function ($query) use ($zonaIds) {
                $query->whereIn('zona_id', $zonaIds);
            })
            ->pluck('estacion_id')
            ->unique()
            ->toArray();

        $pendingInspections = Estacion::query()
            ->whereIn('zona_id', $zonaIds)
            ->whereNotIn('id', $ocupadas)
            ->count();

        return (string) $pendingInspections;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de inspecciones totales pendientes para el cuatrimestre actual');
    }

    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
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

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $zonaIds = $user->zonas()->pluck('zonas.id');
        return parent::getEloquentQuery()
            ->whereHas('estacion', function (Builder $query) use ($zonaIds) {
                $user = auth()->user();
                $zonaIds = $user->zonas()
                    ->pluck('zonas.id')
                    ->toArray();

                $query->whereIn('zona_id', $zonaIds);
            })
        ;
    }
}
