<?php

namespace App\Filament\App\Resources\Courses\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(_('Name'))
                    ->required(),
                Textarea::make('description')
                    ->label(_('Descripción'))
                    ->columnSpanFull(),
                TextInput::make('duration_hours')

                    ->numeric(),
                Toggle::make('requires_renewal')
                    ->required(),
                TextInput::make('renewal_years')
                    ->numeric(),
            ]);
    }
}
