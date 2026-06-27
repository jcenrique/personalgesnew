<?php

namespace App\Filament\Resources\Categoriaelementos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CategoriaelementosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->columns([
                TextColumn::make('nombre_es')
                    ->label(__('Castellano')),
                TextColumn::make('nombre_eu')
                    ->label(__('Euskera')),
                ToggleColumn::make('active')
                    ->label(__('Activo')),

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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
