<?php

namespace App\Filament\Resources\TrainingActions\Pages;

use App\Filament\Resources\TrainingActions\TrainingActionResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTrainingActions extends ListRecords


{
    use HasResizableColumn;

    protected static string $resource = TrainingActionResource::class;
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ,
        ];
    }
}
