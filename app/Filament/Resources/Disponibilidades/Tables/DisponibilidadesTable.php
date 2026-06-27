<?php

namespace App\Filament\Resources\Disponibilidades\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisponibilidadesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))

                    ->sortable(),
                TextColumn::make('year')
                    ->label(__('Año'))
                    ->numeric(),

                TextColumn::make('fecha')
                    ->label(__('Fecha'))
                    ->date('d F Y')
                    ->sortable(),

                TextColumn::make('razon')
                    ->label(__('Razón'))
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                 //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->options(function () {
                        $years =  DB::table('disponibilidades')->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    //por defecto año actual

                    ->searchable()
                    ->placeholder(__('Selecciona un año')),

                SelectFilter::make('user_id')
                    ->label(__('Usuario'))
                    ->options(function (): array {
                        $user = Auth::user();

                        // Si es super_admin o admin, mostrar todos los usuarios
                        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                            return \App\Models\User::orderBy('name')->pluck('name', 'id')->toArray();
                        }

                        // Si NO es admin, obtener zonas del usuario autenticado directamente
                        $zonaIds = $user->zonas()
                            ->pluck('id')
                            ->toArray();

                        // Solo mostrar usuarios de esas zonas
                        return \App\Models\User::whereHas('zonas', function (Builder $q) use ($zonaIds) {
                            $q->whereIn('id', $zonaIds);
                        })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),

            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel(true)
                    ->tooltip(__('Edit'))
                    ->modalWidth(Width::Small),
               DeleteAction::make()
                    ->hiddenLabel(true)
                    ->tooltip(__('Delete'))
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
