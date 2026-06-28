<?php

namespace App\Filament\Resources\Reconocimientos\Tables;

use App\Models\Reconocimiento;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReconocimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('fecha', 'asc')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))

                    ->sortable(),

                TextColumn::make('fecha')
                    ->label(__('Último'))
                    // si la fecha esta entre la  6 meses a la proxima cita color naranja , si es igual o mayor a la actual color rojo, mientras verde
                    ->color(function ($record) {

                        // fecha del reconocimeinto teorico
                        $fecha = $record->fecha->addYear($record->años);
                        $diferencia = $fecha->diffInMonths(now());

                        if ($diferencia >= -6 && $diferencia < 0) {

                            return 'warning';
                        } elseif ($diferencia > -6) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->date('d F Y')
                    ->sortable(),

                TextColumn::make('lugar')
                    ->label(__('Lugar')),
                TextColumn::make('años')
                    ->label(__('Validez'))
                    ->numeric()
                    ->suffix(' '.__('años')),

                TextColumn::make('proxima_cita')
                    ->label(__('Próximo teórico'))
                    ->date('d F Y')
                    ->color('gray')

                    ->state(function ($record) {

                        return $record->fecha->addYear($record->años);
                    }),

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
                SelectFilter::make('user_id')
                    ->label(__('Usuario'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([

                Action::make('generarProximo')
                    ->hiddenLabel(true)
                    ->tooltip(__('Próximo reconocimiento'))
                    ->icon('heroicon-o-calendar-days')
                    ->requiresConfirmation()
                    ->schema([
                        DatePicker::make('fecha')
                            ->label(__('Fecha'))
                            ->native(false)
                            ->reactive()
                            ->format('Y-m-d')
                            ->displayFormat('d F Y')
                            ->default(function ($record) {

                                return $record->fecha->addYear($record->años);
                            })
                            // ->afterStateHydrated(function (DatePicker $component,  $record) {

                            //     $component->date($record->fecha);

                            // })
                            ->closeOnDateSelection()
                            ->locale('es')
                            ->columnSpan(1)
                            ->required(),

                        TextInput::make('lugar')
                            ->columnSpan(1)
                            ->default(function ($record) {
                                return $record->lugar;
                            })
                            ->autocapitalize(true)
                            ->required()
                            ->aboveErrorMessage([
                                Icon::make(Heroicon::Star),
                                'This is the content above the field\'s error message',
                            ])
                            ->label(__('Lugar reconocimiento')),
                        TextInput::make('años')
                            ->label(__('Años proximo reconocimiento'))
                            ->reactive()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(4)
                            ->default(4),
                    ])
                    ->action(function (Reconocimiento $record, $data) {

                        Reconocimiento::create([
                            'user_id' => $record->user_id,
                            'fecha' => $data['fecha'],

                            'lugar' => $data['lugar'],
                            'años' => $data['años'],
                        ]);
                    }),

                EditAction::make()
                    ->modalWidth(Width::Small)
                    ->hiddenLabel(true)
                    ->tooltip(__('Edit')),
                DeleteAction::make()
                    ->hiddenLabel(true)
                    ->modalHeading(function ($record) {

                        $fecha = $record->fecha->translatedFormat('d F Y');

                        return $fecha;
                    })

                    ->tooltip(__('Delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
