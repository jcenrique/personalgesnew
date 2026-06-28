<?php

namespace App\Filament\App\Resources\Computos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ComputoInfolist
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([

                Section::make(__('Detalles').': '.$schema->getRecord()->year)

                    ->columnSpanFull()
                    ->columns(4)

                    ->schema([

                        TextEntry::make('disponible')
                            ->extraAttributes(['class' => 'text-lg font-bold'])
                            ->label(__('Computo'))
                            ->formatStateUsing(function ($record) {
                                $minutos = $record->disponible;
                                $horas = intdiv($minutos, 60);
                                $mins = $minutos % 60;

                                return sprintf('%02d:%02d', $horas, $mins);
                            }),

                        TextEntry::make('disponible')
                            ->extraAttributes(['class' => 'text-lg font-bold'])
                            ->label(__('Solicitado'))
                            ->color('danger')
                            ->formatStateUsing(function ($record) {
                                $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');

                                $horas = intdiv($min_solicitados, 60);
                                $mins = $min_solicitados % 60;

                                return sprintf('%02d:%02d', $horas, $mins);
                            }),

                        TextEntry::make('disponible')
                            ->extraAttributes(['class' => 'text-lg font-bold'])
                            ->label(__('Disponible'))
                            // cambiar el color si es negativo a gris, si es positivo a success y si es cero a warning
                            ->color(function ($record) {
                                $min_disponibles = $record->disponible;
                                $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');
                                $restantes = $min_disponibles - $min_solicitados;
                                if ($restantes < 0) {
                                    return 'danger';
                                } elseif ($restantes > 0) {
                                    return 'success';
                                } else {
                                    return 'warning';
                                }
                            })

                            ->formatStateUsing(function ($record) {
                                $min_disponibles = $record->disponible;
                                $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');
                                $restantes = $min_disponibles - $min_solicitados;
                                // si el resultado es negativo mostrar el signo menos como prefijo
                                $restantes_formateados = sprintf('%02d:%02d', intdiv(abs($restantes), 60), abs($restantes) % 60);
                                if ($restantes < 0) {
                                    $restantes_formateados = '-'.$restantes_formateados;
                                }

                                return $restantes_formateados;
                            }),

                    ])
                    ->icon('far-hourglass-half'),

            ]);
    }
}
