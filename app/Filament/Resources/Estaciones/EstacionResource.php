<?php

namespace App\Filament\Resources\Estaciones;


use App\Filament\Resources\Estaciones\Pages\ListEstaciones;
use App\Filament\Resources\Estaciones\Schemas\EstacionForm;
use App\Filament\Resources\Estaciones\Tables\EstacionesTable;

use App\Models\Estacion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class EstacionResource extends Resource
{
    protected static ?string $model = Estacion::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-house-flag';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $slug = 'estaciones';


    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Registro Inspecciones');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Estación');
    }
    public static function getPluralLabel(): string
    {
        return __('Estaciones');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        return  Estacion::count();
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }


    public static function form(Schema $schema): Schema
    {
        return EstacionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EstacionesTable::configure($table);
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
            'index' => ListEstaciones::route('/'),
            //'create' => CreateEstacion::route('/create'),
            //'edit' =>EditEstacion::route('/{record}/edit'),
        ];
    }
}
