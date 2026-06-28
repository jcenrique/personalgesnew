<?php

namespace App\Filament\App\Resources\Rechazos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RechazosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll(function ($livewire) {

                $livewire->dispatch('refresh-sidebar');

                return '10s';
            })
            ->columns([
               // crear una columna para mostra el tipo de recurso rechazado (sabado, permiso, etc) utilizando la relación polimorfica entre rechazo y el recurso rechazado, mostrando el tipo de recurso y la fecha de disfrute asociada al rechazo
               TextColumn::make('rechazable_type')
                   ->label(__('Tipo de recurso solicitado'))
                   ->formatStateUsing(function ($state) {

                       if ($state === 'App\Models\Sabado') {
                           return __('Sábado');
                       } elseif ($state === 'App\Models\Additionalday') {
                           return __('Día adicional');
                       } elseif ($state === 'App\Models\Computo') {
                           return __('Cómputo');
                       } elseif ($state === 'App\Models\Companyday') {

                           return __('Días de empresa');
                       }

                       // Agrega más condiciones para otros tipos de recursos si es necesario
                       return $state;
                   })
                   // mostrar la fecha de sabado  asociada si el recurso es un sabado, utilizando la relación entre rechazo y sábado para obtener la fecha de sabado trabajado asociada al rechazo
                   ->description(function ($record) {
                       // dd($record->rechazable()->first()->sabado_trabajado);
                       if ($record->rechazable_type === 'App\Models\Sabado') {
                           return $record->rechazable()->latest()->first()->sabado_trabajado?->translatedFormat('d F Y');
                       } elseif ($record->rechazable_type === 'App\Models\Additionalday') {
                           return $record->rechazable()->latest()->first()->year;
                       } elseif ($record->rechazable_type === 'App\Models\Computo') {
                           return $record->rechazable()->latest()->first()->year;
                       } elseif ($record->rechazable_type === 'App\Models\Companyday') {
                           return $record->rechazable()->first()->fecha?->translatedFormat('d F Y');
                       }

                       // Agrega más condiciones para otros tipos de recursos si es necesario
                       return '';
                   }),

               TextColumn::make('fecha_disfrute')
                   ->label(__('Fecha de disfrute solicitada'))
                   ->date('d F Y')
                   ->sortable(),
               TextColumn::make('razon')
                   ->label(__('Motivo de rechazo'))
                   ->wrap(),
               TextColumn::make('created_at')
                   ->dateTime()
                   ->sortable()
                   ->toggleable(isToggledHiddenByDefault: true),
               TextColumn::make('updated_at')
                   ->dateTime()
                   ->sortable()
                   ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make()
                   ->hiddenLabel(true)
                   ->icon('fas-trash')
                   ->tooltip(__('Delete')),
                EditAction::make()
                   ->hiddenLabel(true)
                   ->icon('fas-pencil')
                   ->tooltip(__('Edit')),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),

            ]);
    }
}
