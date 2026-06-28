<?php

namespace App\Filament\Resources\Rechazos\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RechazosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->sortable()
                    ->searchable(),
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
                            return $record->rechazable()->latest()->first()->fecha?->translatedFormat('d F Y');
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

                TextColumn::make(('deleted_at'))
                    ->label(__('Eliminado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Creado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Actualizado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // filtro por usuario utilizando la relación entre rechazo y usuario para obtener el nombre del usuario asociado al rechazo
                SelectFilter::make('user_id')
                    ->label(__('User'))
                    ->options(function () {
                        // Obtener los usuarios que han realizado rechazos
                        $users = User::whereHas('rechazos')->pluck('name', 'id');

                        return $users;
                    })
                    ->searchable()
                    ->placeholder(__('Selecciona un usuario')),

                // filtro por tipo de recurso utilizando la relación polimorfica entre rechazo y el recurso rechazado para obtener el tipo de recurso asociado al rechazo
                SelectFilter::make('rechazable_type')
                    ->label(__('Tipo de recurso solicitado'))
                    ->options([
                        'App\Models\Sabado' => __('Sábado'),
                        'App\Models\Additionalday' => __('Día adicional'),
                        // Agrega más opciones para otros tipos de recursos si es necesario
                    ])
                    ->searchable()
                    ->placeholder(__('Selecciona un tipo de recurso')),

            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel(true)
                    ->icon('fas-pencil')
                    ->tooltip(__('Edit')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
