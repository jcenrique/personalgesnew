<?php

namespace App\Filament\Resources\Elementoinspecciones;

use App\Filament\Resources\Elementoinspecciones\Pages\CreateElementoinspeccion;
use App\Filament\Resources\Elementoinspecciones\Pages\EditElementoinspeccion;
use App\Filament\Resources\Elementoinspecciones\Pages\ListElementoinspeccions;
use App\Filament\Resources\Elementoinspecciones\Schemas\ElementoinspeccionForm;
use App\Filament\Resources\Elementoinspecciones\Tables\ElementoinspeccionesTable;

use App\Models\Elementoinspeccion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ElementoinspeccionResource extends Resource
{
    protected static ?string $model = Elementoinspeccion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::QueueList;

    protected static ?string $recordTitleAttribute = 'castellano';

    protected static ?string $slug = 'elementoinspecciones';

    protected static ?int $navigationSort= 23;

        public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Registro Inspecciones');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Elemento inspección');
    }
    public static function getPluralLabel(): string
    {
        return __('Elementos inspección');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
                 return  Elementoinspeccion::count();
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }


    public static function form(Schema $schema): Schema
    {
        return ElementoinspeccionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ElementoinspeccionesTable::configure($table);
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
            'index' => ListElementoinspeccions::route('/'),
           // 'create' => CreateElementoinspeccion::route('/create'),
            //'edit' => EditElementoinspeccion::route('/{record}/edit'),
        ];
    }
}
