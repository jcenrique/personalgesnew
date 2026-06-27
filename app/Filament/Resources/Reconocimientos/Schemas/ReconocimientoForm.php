<?php

namespace App\Filament\Resources\Reconocimientos\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReconocimientoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('user_id')
                    ->columnSpan(1)
                    ->hiddenOn('edit')
                    ->disabledOn('edit')
                    ->label(__('User'))
                    ->options(function ($record) {
                        return User::query()
                            ->when($record, function ($q) use ($record) {
                                // Permitir el usuario del registro actual
                                $q->orWhere('id', $record->user_id);
                            })
                            ->whereDoesntHave('reconocimientos', function ($q) use ($record) {
                                // Ignorar el reconocimiento actual al editar
                                if ($record) {
                                    $q->where('id', '!=', $record->id);
                                }
                            })
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(true)
                    ->required(),
                DatePicker::make('fecha')
                    ->label(__('Fecha'))
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('d F Y')
                    ->closeOnDateSelection()
                    ->locale('es')
                    ->columnSpan(1)
                    ->required(),

                TextInput::make('lugar')
                    ->columnSpan(1)
                    ->label(__('Lugar reconocimiento'))
                    ->required(),
                TextInput::make('años')
                    ->label(__('Años proximo reconocimiento'))
                    ->integer()
                    ->minValue(1)
                    ->maxValue(4)
                    ->default(4),
            ]);
    }
}
