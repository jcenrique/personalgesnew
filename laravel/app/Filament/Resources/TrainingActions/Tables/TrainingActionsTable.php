<?php

namespace App\Filament\Resources\TrainingActions\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingActionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

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

            ->actions([
                Action::make('export_attendees_pdf')
                    ->label(__('Asistentes a PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('training-actions.attendees-pdf', ['trainingAction' => $record]))
                    ->openUrlInNewTab(),
            ]);

    }
}
