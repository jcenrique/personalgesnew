<?php

namespace App\Filament\App\Resources\Additionaldays;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Additionaldays\Pages\ListAdditionaldays;
use App\Filament\App\Resources\Additionaldays\Schemas\AdditionaldayForm;
use App\Filament\App\Resources\Additionaldays\Tables\AdditionaldaysTable;
use App\Models\Additionalday;
use App\Models\Disfrute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class AdditionaldayResource extends Resource
{
    protected static ?string $model = Additionalday::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    // establecer el orden en el menu
    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'year';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Día adicional');
    }

    public static function getPluralLabel(): string
    {
        return __('Días adicionales');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {

        // obtener los dias adicionales del usuario para el año actual
        $dias_adicionales_totales = Additionalday::where('user_id', Auth::id())
            ->where('year', now()->year)->count();

        // contar los días adicionales  se han solicitado disfrutar
        $dias_adicionales_disfrutados = Disfrute::where('user_id', Auth::id())
            ->where('disfrutable_type', Additionalday::class)
            ->where('status', StatusSolicitudes::Aprobado)->count();

        $dias_adicionales_disponibles = $dias_adicionales_totales - $dias_adicionales_disfrutados;

        return $dias_adicionales_disponibles;
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de días adicionales totales disponibles');
    }

    public static function form(Schema $schema): Schema
    {
        return AdditionaldayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdditionaldaysTable::configure($table);
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
            'index' => ListAdditionaldays::route('/'),
            // 'create' => CreateAdditionalday::route('/create'),
            // 'edit' => EditAdditionalday::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery();

    }
}
