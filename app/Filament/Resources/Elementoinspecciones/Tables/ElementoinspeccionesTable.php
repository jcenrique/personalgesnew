<?php

namespace App\Filament\Resources\Elementoinspecciones\Tables;

use App\Models\Elementoinspeccion;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ElementoinspeccionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('categoria.nombre_es')

                    ->getDescriptionFromRecordUsing(fn (Elementoinspeccion $record): string => $record->categoria->nombre_eu)
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
            ->defaultPaginationPageOption(25)
            ->groupingSettingsHidden()

            ->defaultGroup('categoria.nombre_es')
            ->columns([

                TextColumn::make('nombre_es')
                    ->label(__('Elemento'))
                    ->color('info')
                    ->description(function ($record) {
                        return $record->nombre_eu;
                    })
                    ->searchable(),

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
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
