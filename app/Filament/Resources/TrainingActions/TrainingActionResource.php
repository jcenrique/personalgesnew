<?php

namespace App\Filament\Resources\TrainingActions;

use App\Filament\Resources\TrainingActions\Pages\CreateTrainingAction;
use App\Filament\Resources\TrainingActions\Pages\EditTrainingAction;
use App\Filament\Resources\TrainingActions\Pages\ListTrainingActions;
use App\Filament\Resources\TrainingActions\RelationManagers\AttendeesRelationManager;
use App\Filament\Resources\TrainingActions\Schemas\TrainingActionForm;
use App\Filament\Resources\TrainingActions\Tables\TrainingActionsTable;
use App\Models\TrainingAction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Override;
use UnitEnum;

class TrainingActionResource extends Resource
{
    protected static ?string $model = TrainingAction::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-user-graduate';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Formación');
    }

    // funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Acción formativa');
    }

    public static function getPluralLabel(): string
    {
        return __('Acciones formativas');
    }

    public static function getNavigationBadge(): ?string
    {
        $pendingCount = TrainingAction::query()
            ->whereDate('end_date', '>=', now()->toDateString())
            ->count();

        return $pendingCount > 0 ? (string) $pendingCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    #[Override]
    public static function getNavigationBadgeTooltip(): string|Htmlable|null
    {
        return __('Acciones formativas pendientes o programadas');
    }

    public static function form(Schema $schema): Schema
    {
        return TrainingActionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingActionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AttendeesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingActions::route('/'),
            'create' => CreateTrainingAction::route('/create'),
            'edit' => EditTrainingAction::route('/{record}/edit'),
        ];
    }
}
