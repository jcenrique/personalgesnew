<?php

namespace App\Filament\Resources\TrainingActions\Pages;

use App\Filament\Resources\TrainingActions\TrainingActionResource;
use App\Models\TrainingAction;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTrainingActions extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = TrainingActionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $today = now()->toDateString();

        $allCount = TrainingAction::query()->count();
        $pendingCount = TrainingAction::query()
            ->whereDate('end_date', '>=', $today)
            ->count();
        $pastCount = TrainingAction::query()
            ->whereDate('end_date', '<', $today)
            ->count();

        return [
            'todas' => Tab::make(__('Todas'))
                ->badge((string) $allCount),
            'pendientes' => Tab::make(__('Pendientes / programadas'))
                ->badge((string) $pendingCount)
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereDate('end_date', '>=', now()->toDateString())),
            'pasadas' => Tab::make(__('Pasadas'))
                ->badge((string) $pastCount)
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->whereDate('end_date', '<', now()->toDateString())),
        ];
    }
}
