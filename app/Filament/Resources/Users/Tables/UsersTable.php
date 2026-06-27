<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable(),

                TextColumn::make('email')
                ->wrapHeader()
                    ->label(__('Email address'))
                    ->searchable(),




                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->getStateUsing(function ($record) {

                        $roles_user = $record->roles;
                        return $roles_user
                            ->pluck('name')

                            ->unique()
                            ->map(fn($name) => ucwords(str_replace('_', ' ', $name)))
                            ->implode(', ');
                    })
                    ->badge()
                    ->separator(', ')
                    ->searchable(),
                TextColumn::make('locale')
                    ->label(__('Preferred Language'))
                    ->wrapHeader()
                    ->searchable(),
                TextColumn::make('codigo_agente')
                    ->label(__('Código agente'))
                    ->wrapHeader()
                    ->searchable(),

                TextColumn::make('residencias.name')
                    ->label(__('Residencias'))
                    ->badge()
                    ->color('info')
                    ->separator(', ')
                    ->searchable(),
                TextColumn::make('zonas.name')
                    ->label(__('Zonas'))
                    ->badge()
                    ->color('info')
                    ->separator(', '),
                TextColumn::make('status')

                    ->badge()
                    ->color(function ($state) {

                        if ($state) {
                            return 'success';
                        } else {
                            return  'danger';
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return __('Activo');
                        } else {
                            return __('Inactivo');
                        }
                    })
                    ->label(__('Estado')),
                IconColumn::make('notify')
                    ->label(__('Email Notificación'))
                    ->wrapHeader()
                    ->boolean()
                    ->color(function ($state) {
                        if ($state) {
                            return 'success';
                        } else {
                            return  'danger';
                        }
                    }),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime('d/m/Y H:i:s')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')

                    ->multiple()
                    ->label('Roles'),

                SelectFilter::make('residencias')
                    ->relationship('residencias', 'name')

                    ->multiple()
                    ->label('Residencias'),
                SelectFilter::make('zonas')
                    ->relationship('zonas', 'name')
                    ->multiple()
                    ->label('Zonas'),

            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip(__('Edit'))
                    ->hiddenLabel(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
