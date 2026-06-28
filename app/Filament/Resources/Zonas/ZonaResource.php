<?php

namespace App\Filament\Resources\Zonas;

use App\Filament\Resources\Zonas\Pages\CreateZona;
use App\Filament\Resources\Zonas\Pages\EditZona;
use App\Filament\Resources\Zonas\Pages\ListZonas;
use App\Filament\Resources\Zonas\RelationManagers\ResidenciasRelationManager;
use App\Filament\Resources\Zonas\Schemas\ZonaForm;
use App\Filament\Resources\Zonas\Tables\ZonasTable;
use App\Models\Zona;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ZonaResource extends Resource
{
    protected static ?string $model = Zona::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-building-flag';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('Zonas y Residencias');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Zonas');
    }

    public static function getModelLabel(): string
    {
        return __('Zona');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return 'Admin';
    }

    public static function form(Schema $schema): Schema
    {
        return ZonaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZonasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ResidenciasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListZonas::route('/'),
            // 'create' => CreateZona::route('/create'),
            'edit' => EditZona::route('/{record}/edit'),
        ];
    }
}
