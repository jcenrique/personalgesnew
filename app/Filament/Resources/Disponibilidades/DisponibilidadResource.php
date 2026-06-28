<?php

namespace App\Filament\Resources\Disponibilidades;

use App\Filament\Resources\Disponibilidades\Pages\ListDisponibilidades;
use App\Filament\Resources\Disponibilidades\Schemas\DisponibilidadForm;
use App\Filament\Resources\Disponibilidades\Tables\DisponibilidadesTable;
use App\Models\Disponibilidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class DisponibilidadResource extends Resource
{
    protected static ?string $model = Disponibilidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static ?string $recordTitleAttribute = 'fecha';

    protected static ?string $slug = 'disponibilidades';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Disponibilidad');
    }

    public static function getPluralLabel(): string
    {
        return __('Disponibilidades');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        // obtener los dias adicionales del usuario para el año actual
        $dias_adicionales_totales = static::getEloquentQuery()->count();

        return $dias_adicionales_totales;
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de disponiblidades totales solicitadas');
    }

    public static function form(Schema $schema): Schema
    {
        return DisponibilidadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DisponibilidadesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDisponibilidades::route('/'),
            // 'create' => CreateDisponibilidad::route('/create'),
            // 'edit' => EditDisponibilidad::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user.residencias', function (Builder $query) {
                $user = auth()->user();
                $zonaIds = $user->zonas()
                    ->pluck('zonas.id')
                    ->toArray();

                $query->whereIn('zona_id', $zonaIds);
            });
    }
}
