<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Zona;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    // no editable
                    ->disabled(true),

                TextInput::make('email')
                    ->label(__('Email address'))
                    ->email()
                    ->disabled(true),
                // Using Select Component
                Select::make('roles')
                    ->label(__('Roles'))
                    ->relationship(name: 'roles', titleAttribute: 'name')

                    ->getOptionLabelFromRecordUsing(function ($record) {
                        return ucwords(str_replace('_', ' ', $record->name));
                    })
                    ->reactive()
                    ->multiple()
                    ->preload()
                    ->searchable(),

                DateTimePicker::make('email_verified_at')
                    ->label(__('Email Verified At'))

                    ->displayFormat('H:i d/M/Y')
                    ->format('Y-m-d H:i:s'),

                // ->placeholder('Selecciona la fecha de verificación'),          // Opcional: Lunes como primer día
                TextInput::make('codigo_agente')
                    ->label(__('Código agente'))
                    ->numeric()

                    ->disabled(true),

                Select::make('locale')
                    ->label(__('Preferred Language'))
                    ->options([
                        'eu' => __('Euskera'),
                        'es' => __('Castellano'),
                    ])
                    ->selectablePlaceholder(false)

                    ->required(),

                Select::make('residencias')
                    ->relationship('residencias', 'name')
                    ->multiple()
                    ->reactive()
                    ->searchable()
                    // si se ha elegido la residencia marcar la zona a la que pertenece esa residencia
                    ->afterStateUpdated(function (callable $set, $state) {
                        if (count($state) > 0) {
                            $zona_id = Zona::whereHas('residencias', function (Builder $query) use ($state) {
                                $query->whereIn('residencias.id', $state);
                            })->pluck('id')->toArray();
                            $set('zonas', $zona_id);
                        }
                    })
                    ->required()
                    ->preload(),
                // nuevo campo de status boolean activo o inactivo
                Toggle::make('status')
                    ->label(fn (Get $get) => $get('status') ? 'Activo' : 'Inactivo')
                    ->reactive()
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline()
                    ->onIcon(Heroicon::UserPlus)
                    ->offIcon(Heroicon::UserMinus)
                    ->required(),
                Toggle::make('notify')
                    ->label(fn (Get $get) => $get('notify') ? 'Notify: ON' : 'Notify: OFF')
                    ->reactive()
                    ->onColor('success')
                    ->offColor('danger')
                    ->inline()
                    ->onIcon(Heroicon::BellAlert)
                    ->offIcon(Heroicon::BellSlash)
                    ->required(),
                CheckboxList::make('zonas')
                    ->label('Zonas')
                    ->required()
                    ->relationship('zonas', 'nombre')

                    ->options(
                        fn () => Auth::user()->hasRole('super_admin')
                            ? Zona::pluck('name', 'id')
                            : Auth::user()->zonas->pluck('name', 'id')
                    ),

            ]);
    }
}
