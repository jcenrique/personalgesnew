<?php

namespace App\Filament\App\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Nombre'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('Descripción'))
                    ->limit(50),
                TextColumn::make('last_attendance_date')
                    ->label(__('Fecha asistencia'))
                    ->date('d F Y')
                    ->getStateUsing(function ($record) {
                        $user = auth()->user();

                        $lastAction = $record->trainingActions()
                            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                            ->orderByDesc('training_actions.end_date')
                            ->first();

                        return $lastAction?->start_date
                            ? $lastAction->start_date->translatedFormat('d F Y')
                            : '—';
                    })
                    ->sortable(),

                TextColumn::make('duration_hours')
                    ->label(__('Horas'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('requires_renewal')
                    ->label(__('Requiere renovación'))
                    ->boolean(),

                TextColumn::make('renewal_remaining_years')
                    ->label(__('Años restantes'))
                    ->getStateUsing(function ($record) {
                        $user = auth()->user();

                        // Si el curso no tiene renovación
                        if (! $record->requires_renewal || ! $record->renewal_years) {
                            return '—';
                        }

                        // Última acción formativa del usuario para este curso
                        $lastAction = $record->trainingActions()
                            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                            ->orderByDesc('training_actions.end_date')
                            ->first();

                        if (! $lastAction) {
                            return '—';
                        }

                        $lastDate = $lastAction->end_date;
                        $yearsPassed = $lastDate->diffInYears(now());
                        $remaining = intval($record->renewal_years - $yearsPassed);

                        return $remaining > 0 ? $remaining.' '.__('años') : '0 '.__('años');
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make()

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
