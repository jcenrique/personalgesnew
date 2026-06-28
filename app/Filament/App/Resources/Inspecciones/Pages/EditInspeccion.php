<?php

namespace App\Filament\App\Resources\Inspecciones\Pages;

use App\Filament\App\Resources\Inspecciones\InspeccionesResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditInspeccion extends EditRecord
{
    protected static string $resource = InspeccionesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            Action::make('pdf')
                ->hidden(function ($record) {
                    return $record->type == 'especial';
                })
                ->color('info')
                ->hiddenLabel(true)
                ->tooltip('Descargar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record) => route('inspecciones.export-pdf', ['inspeccion' => $record]))
                ->openUrlInNewTab(),

            Action::make('pdf')
                ->hidden(function ($record) {
                    return $record->type == 'periodica';
                })
                ->color('success')
                ->hiddenLabel(true)
                ->tooltip('Descargar PDF inspección especial')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn ($record) => route('inspecciones.export-especial-pdf', ['inspeccion' => $record]))
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        //
        if (
            $data['type'] === 'especial'
            && (! empty($data['user_id_1'])
                || ! empty($data['user_id_2']))
        ) {

            return $data;
        } elseif ($data['type'] === 'especial') {
            Notification::make()
                ->title(__('Error de validación'))
                ->body(__('Al menos se debe completar 1 de los campos de JS o el TR'))
                ->danger()

                ->persistent()
                ->send();

            $this->halt();
        }

        if (
            $data['type'] === 'periodica'
            && (! empty($data['user_id_1'])
                && ! empty($data['user_id_2']))
        ) {

            return $data;
        } elseif ($data['type'] === 'periodica') {

            Notification::make()
                ->title(__('Error de validación'))
                ->body(__('Los campos JS y TR no pueden estar vacíos'))
                ->danger()

                ->persistent()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
