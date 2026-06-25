<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Filament\Resources\TrainingActions\TrainingActionResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingactionsRelationManager extends RelationManager
{
    use HasResizableColumn;
    protected static string $relationship = 'trainingactions';

    protected static ?string $relatedResource = TrainingActionResource::class;



    public  function table(Table $table): Table
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

            ->filters([])
            ->recordActions([
                Action::make('export_attendees_pdf')
                    ->hiddenLabel(true)
                    ->tooltip(__('Exportar asistentes a PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('training-actions.attendees-pdf', ['trainingAction' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make()

                    ->hiddenLabel(true)
                    ->tooltip(__('Edit'))

            ])
            ->headerActions([
                CreateAction::make('crear')
                    ->modalWidth(Width::Small)
            ]);
    }
}
