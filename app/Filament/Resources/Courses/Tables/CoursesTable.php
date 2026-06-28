<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('name')
                    ->label(__('Nombre del curso'))
                    ->width('10%')
                    ->description(fn ($record) => Str::words($record->description, 10, '...'))
                    ->tooltip(fn ($record) => $record->description)
                    ->searchable()->sortable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->label(__('Roles obligatorios'))
                    ->getStateUsing(function ($record) {

                        $roles_user = $record->roles;

                        return $roles_user
                            ->pluck('name')

                            ->unique()
                            ->map(fn ($name) => ucwords(str_replace('_', ' ', $name)))
                            ->implode(', ');
                    })
                    ->wrap()
                    ->separator(', '),
                IconColumn::make('requires_renewal')
                    ->label(__('Requiere renovación'))
                    ->wrapHeader()
                    ->boolean(),
                TextColumn::make('duration_hours')
                    ->label(__('Horas')),
                TextColumn::make('renewal_text')

                    ->badge()
                    ->label(__('Renovación'))
                    ->colors([
                        'gray' => fn ($record) => ! $record->requires_renewal,
                        'success' => fn ($record) => $record->requires_renewal,
                    ]),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
