<?php

namespace App\Filament\Resources\Companydays;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Companydays\Pages\ListCompanydays;
use App\Filament\Resources\Companydays\Schemas\CompanydayForm;
use App\Filament\Resources\Companydays\Tables\CompanydaysTable;
use App\Models\Companyday;
use App\Models\Disfrute;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use UnitEnum;

class CompanydayResource extends Resource
{
    protected static ?string $model = Companyday::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-calendar-day';

    protected static ?string $recordTitleAttribute = 'fecha';



    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Día pedido Empresa');
    }
    public static function getPluralLabel(): string
    {
        return __('Días pedidos Empresa');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        //obtener los dias adicionales del usuario para el año actual
        $dias_totales = static::getEloquentQuery()->count();

        $dias_disfrutados = static::getEloquentQuery()->whereHas('disfrute', function (Builder $query) {
            $query->where('status', StatusSolicitudes::Aprobado);
        })->count();

        //contar los días adicionales  se han solicitado disfrutar
        // $dias_disfrutados = Disfrute::where('disfrutable_type', Companyday::class)
        //    ->where('status', StatusSolicitudes::Aprobado)->count();

        $dias_disponibles = $dias_totales - $dias_disfrutados;




        return  $dias_disponibles;
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de dias pedidos por la empresa disponibles para disfrutar');
    }

    public static function form(Schema $schema): Schema
    {
        return CompanydayForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanydaysTable::configure($table);
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
            'index' => ListCompanydays::route('/'),
            //  'create' => CreateCompanyday::route('/create'),
            //'edit' => EditCompanyday::route('/{record}/edit'),
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
            })
            ->with('disfrute');
    }
}
