<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->components([
                TextInput::make('name')
                    ->label(__('Nombre del curso'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('duration_hours')
                    ->label(__('Duración en horas'))
                    ->numeric(),

                Select::make('roles')
                    ->label(__('Roles obligatorios'))
                    ->multiple()
                    ->preload()
                    ->columnSpan(2)
                    ->relationship(name: 'roles', titleAttribute: 'name')

                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return ucwords(str_replace('_', ' ', $record->name));
                    }),

                Textarea::make('description')
                    ->label(__('Descripción del curso'))
                    ->columnSpan(2),

                Toggle::make('requires_renewal')
                    ->inline(false)
                    ->label(__('Requiere renovación'))
                    ->reactive(),

                TextInput::make('renewal_years')
                    ->label(__('Años para renovación'))
                    ->numeric()
                    ->visible(fn ($get) => $get('requires_renewal')),
            ]);
    }
}
