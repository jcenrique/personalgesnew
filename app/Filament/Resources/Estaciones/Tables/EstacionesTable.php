<?php

namespace App\Filament\Resources\Estaciones\Tables;

use App\Models\Estacion;
use App\Models\Zona;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class EstacionesTable
{
    public static function configure(Table $table): Table
    {
        return $table

          ->groups([
                Group::make('zona.name')
                    //obtener el numero de estaciones por zona
                    ->getDescriptionFromRecordUsing(fn(Estacion $record): string => Zona::find($record->zona_id)->estaciones()->count() . ' ' . __('estaciones'))
                    
                    ->titlePrefixedWithLabel(false)
                    ->collapsible(),
            ])
        ->defaultGroup('zona.name')
            ->groupingSettingsHidden()
        ->defaultSort('pk', 'asc')
            ->columns([


                TextColumn::make('name')
                    ->label(__('Estación'))
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                 TextColumn::make('nemonico')
                    ->label(__('Nemónico'))
                    ->sortable(),

                     TextColumn::make('pk')
                    ->label(__('PK.'))
                    ->numeric(3,',')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('zona_id')
                    ->relationship('zona' , 'name')
            ])
            ->recordActions([
                EditAction::make()
                     ->modalWidth(Width::Small)
                     ->hiddenLabel(true)
                     ->tooltip(__('Edit')),
                DeleteAction::make()

                     ->hiddenLabel(true)
                     ->tooltip(__('Delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
