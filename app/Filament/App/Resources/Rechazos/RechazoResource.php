<?php

namespace App\Filament\App\Resources\Rechazos;

use App\Filament\App\Resources\Rechazos\Pages\CreateRechazo;
use App\Filament\App\Resources\Rechazos\Pages\EditRechazo;
use App\Filament\App\Resources\Rechazos\Pages\ListRechazos;
use App\Filament\App\Resources\Rechazos\Schemas\RechazoForm;
use App\Filament\App\Resources\Rechazos\Tables\RechazosTable;
use App\Models\Rechazo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RechazoResource extends Resource
{
    protected static ?string $model = Rechazo::class;

    protected static string|BackedEnum|null $navigationIcon = 'far-circle-xmark';

    // establecer el orden en el menu
    protected static ?int $navigationSort = 30;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Rechazo');
    }

    public static function getPluralLabel(): string
    {
        return __('Rechazos');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('user_id', Auth::id())->count();
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return RechazoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RechazosTable::configure($table);
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
            'index' => ListRechazos::route('/'),
            // 'create' => CreateRechazo::route('/create'),
            // 'edit' => EditRechazo::route('/{record}/edit'),
        ];
    }
}
