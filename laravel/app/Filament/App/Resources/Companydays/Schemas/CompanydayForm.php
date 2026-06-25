<?php

namespace App\Filament\App\Resources\Companydays\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompanydayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('razon')
                    ->required(),
            ]);
    }
}
