<?php

namespace App\Filament\Resources\Additionaldays\Pages;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Additionaldays\AdditionaldayResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAdditionalday extends EditRecord
{
    protected static string $resource = AdditionaldayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        if ($this->record && $this->record->disfrute) {
            $data['disfrute']['fecha_disfrute'] = $this->record->disfrute->fecha_disfrute;
            $data['disfrute']['status'] = $this->record->disfrute->status;
        } elseif ($this->record && ! $this->record->disfrute) {
            $data['disfrute']['status'] = StatusSolicitudes::Disponible;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $this->dispatch('updateAuditsRelationManager');
    }
}
