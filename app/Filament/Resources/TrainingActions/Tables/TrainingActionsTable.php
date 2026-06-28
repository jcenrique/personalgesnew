<?php

namespace App\Filament\Resources\TrainingActions\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingActionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label(__('Curso'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company_name')
                    ->label(__('Empresa'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('trainer_name')
                    ->label(__('Formador'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('Tipo'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('mode')
                    ->label(__('Modalidad'))
                    ->sortable(),
                TextColumn::make('location')
                    ->label(__('Lugar'))
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label(__('Inicio'))
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label(__('Fin'))
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label(__('Usuario (formaciones asignadas)'))
                    ->searchable()
                    ->options(fn(): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        $userId = $data['value'] ?? null;

                        if (! $userId) {
                            return $query;
                        }

                        return $query->whereHas('users', function (Builder $userQuery) use ($userId): void {
                            $userQuery->where('users.id', $userId);
                        });
                    }),
            ])

            ->actions([
                Action::make('export_attendees_pdf')
                    ->label(__('Asistentes a PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => route('training-actions.attendees-pdf', ['trainingAction' => $record]))
                    ->openUrlInNewTab(),
            ]);
    }
}
