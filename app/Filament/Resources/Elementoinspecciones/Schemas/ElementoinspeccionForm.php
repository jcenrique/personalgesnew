<?php

namespace App\Filament\Resources\Elementoinspecciones\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ElementoinspeccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Select::make('categoriaelemento_id')
                        ->label(__('Categoría'))
                        //  ->relationship('categoria' , 'castellano')
                        ->relationship(
                            name: 'categoria',
                            modifyQueryUsing: fn (Builder $query) => $query->orderBy('nombre_es'),
                        )
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->nombre_es} / {$record->nombre_eu}"),
                    Toggle::make('active')
                        ->label(__('Activo'))
                        ->inline(false)
                        ->default(true)
                        ->required(),
                ]),
                Group::make([
                    TextInput::make('nombre_eu')
                        ->label(__('Nombre en Euskera'))

                        ->required(),
                    TextInput::make('nombre_es')
                        ->label(__('Nombre en Castellano'))

                        ->required(),
                ]),

            ]);
    }
}
