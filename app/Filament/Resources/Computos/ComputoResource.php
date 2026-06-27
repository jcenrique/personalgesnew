<?php

namespace App\Filament\Resources\Computos;


use App\Filament\Resources\Computos\Pages\ListComputos;

use App\Filament\Resources\Computos\Pages\ViewComputo;
use App\Filament\Resources\Computos\Schemas\ComputoForm;
use App\Filament\Resources\Computos\Schemas\ComputoInfolist;
use App\Filament\Resources\Computos\Tables\ComputosTable;
use App\Models\Computo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ComputoResource extends Resource
{
    protected static ?string $model = Computo::class;



    protected static string|BackedEnum|null $navigationIcon = 'fas-clock';

    protected static ?string $recordTitleAttribute = 'year';

    //establecer el orden en el menu
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Cómputo');
    }
    public static function getPluralLabel(): string
    {
        return __('Cómputos');
    }
    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        //obtener la suma de los minutos disponibles del año actual de todos los usuarios
        $min_disponibles  = static::getEloquentQuery()->where('year', now()->year)->sum('disponible');
        //a partir de la tabla computos, obtener los minutos solicitados asociados a la relacion morph
        //de computo con la tabla disfrutes del año actual de todos los usuarios,
        //para esto se puede usar el metodo whereHas para filtrar los registros de computos que tienen
        // relacion con disfrutes del año actual y luego usar el metodo sum para obtener la suma
        //de los minutos solicitados de esos registros filtrados la suma debe ser los minutos solicitados
        //de todos los disfrutes asociados a los computos del año actual de todos los usuarios
        $min_solicitados = static::getEloquentQuery()->where('year', now()->year)
            ->withSum(['disfrutes as minutos_solicitados_sum' => function ($q) {
                $q->where('minutos_solicitados', '>', 0);
            }], 'minutos_solicitados')
            ->get()
            ->sum('minutos_solicitados_sum');

        $restantes = $min_disponibles - $min_solicitados;


        $horas = intdiv($restantes, 60);

        $mins  = $restantes % 60;

        return sprintf('%02d:%02d', $horas, $mins);
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Horas totales disponibles año actual');
    }


    public static function form(Schema $schema): Schema
    {
        return ComputoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ComputoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComputosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DisfrutesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComputos::route('/'),
            'add-computos' => Pages\AddComputos::route('/add-computos'),
            // 'create' => CreateComputo::route('/create'),
            'view' => ViewComputo::route('/{record}'),
            //  'edit' => EditComputo::route('/{record}/edit'),

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
           ;
    }
}
