<?php

namespace App\Filament\Resources\Categoriaelementos;

use App\Filament\Resources\Categoriaelementos\Pages\CreateCategoriaelemento;
use App\Filament\Resources\Categoriaelementos\Pages\EditCategoriaelemento;
use App\Filament\Resources\Categoriaelementos\Pages\ListCategoriaelementos;
use App\Filament\Resources\Categoriaelementos\Schemas\CategoriaelementoForm;
use App\Filament\Resources\Categoriaelementos\Tables\CategoriaelementosTable;
use App\Models\Categoriaelemento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CategoriaelementoResource extends Resource
{
    protected static ?string $model = Categoriaelemento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $recordTitleAttribute = 'nombre_es';

    protected static ?int $navigationSort = 22;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Registro Inspecciones');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Categoría elemento');
    }

    public static function getPluralLabel(): string
    {
        return __('Categorías elementos');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        return Categoriaelemento::count();
    }

    // badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function form(Schema $schema): Schema
    {
        return CategoriaelementoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriaelementosTable::configure($table);
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
            'index' => ListCategoriaelementos::route('/'),
            // 'create' => CreateCategoriaelemento::route('/create'),
            // 'edit' => EditCategoriaelemento::route('/{record}/edit'),
        ];
    }
}
