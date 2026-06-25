<?php

namespace App\Filament\App\Resources\Disponibilidades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisponibilidadesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('year')
                    ->label(__('Año')),
                TextColumn::make('fecha')
                    ->label(__('Fecha'))
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('razon')
                    ->label(__('Razón'))
                    ,

            ])
            ->filters([
                // //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->preload(true)
                    //mostrar una lista de años, en los que se dispone de día adicional
                    ->options(function () {
                        $years =  DB::table('disponibilidades')->where('user_id', Auth::id())->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    //por defecto año actual

                    ->searchable()
                    ->placeholder(__('Selecciona un año')),

            ])
            ->recordActions([
               // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                   // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
