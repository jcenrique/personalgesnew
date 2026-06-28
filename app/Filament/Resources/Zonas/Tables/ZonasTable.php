<?php

namespace App\Filament\Resources\Zonas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ZonasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Nombre de la zona'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('residencias.name')
                    ->label(__('Residencias'))
                    ->counts('residencias')
                    ->color('info')
                    ->badge('info'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Edit')),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Delete')),
            ])
            ->headerActions([

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
