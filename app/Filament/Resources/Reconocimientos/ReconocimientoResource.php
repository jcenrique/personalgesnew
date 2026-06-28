<?php

namespace App\Filament\Resources\Reconocimientos;

use App\Filament\Resources\Reconocimientos\Pages\ListReconocimientos;
use App\Filament\Resources\Reconocimientos\Schemas\ReconocimientoForm;
use App\Filament\Resources\Reconocimientos\Tables\ReconocimientosTable;
use App\Models\Reconocimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ReconocimientoResource extends Resource
{
    protected static ?string $model = Reconocimiento::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-briefcase-medical';

    protected static ?string $recordTitleAttribute = 'fecha';

    protected static ?int $navigationSort = 50;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Reconocimiento');
    }

    public static function getPluralLabel(): string
    {
        return __('Reconocimientos');
    }

    public static function form(Schema $schema): Schema
    {
        return ReconocimientoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReconocimientosTable::configure($table);
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
            'index' => ListReconocimientos::route('/'),
            // 'create' => CreateReconocimiento::route('/create'),
            // 'edit' => EditReconocimiento::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {

    //       return static::getModel()::query()
    //     ->latestPerUser();
    // }
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
            ->latestPerUser();
    }
}
