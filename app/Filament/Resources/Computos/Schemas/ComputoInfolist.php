<?php

namespace App\Filament\Resources\Computos\Schemas;

use App\Models\Computo;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class ComputoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make(__('Detalles'))

                    ->schema([
                        TextEntry::make('year')
                            ->label(__('Año'))
                            ->badge('primary')
                            ->numeric(),
                        TextEntry::make('user.name')
                            ->color('info')
                            ->extraAttributes(['class' => 'text-lg font-bold'])
                            ->label(__('Usuario')),
                    ]),

                Section::make(__('Datos del Computo'))

                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextEntry::make('disponible')
                                    ->extraAttributes(['class' => 'text-lg font-bold'])
                                    ->label(__('Computo'))
                                    ->formatStateUsing(function ($record) {
                                        $minutos = $record->disponible;
                                        $horas = intdiv($minutos, 60);
                                        $mins  = $minutos % 60;

                                        return sprintf('%02d:%02d', $horas, $mins);
                                    }),

                                TextEntry::make('disponible')
                                    ->extraAttributes(['class' => 'text-lg font-bold'])
                                    ->label(__('Solicitado'))
                                    ->color('danger')
                                    ->formatStateUsing(function ($record) {
                                        $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');
                                        $horas = intdiv($min_solicitados, 60);
                                        $mins  = $min_solicitados % 60;

                                        return sprintf('%02d:%02d', $horas, $mins);
                                    }),
                            ]),


                        TextEntry::make('disponible')
                            ->extraAttributes(['class' => 'text-lg font-bold'])
                            ->label(__('Disponible'))
                            ->color(function ($record) {
                                $minutos_computo = $record->disponible;
                                $minutos_disfrutados = $record->disfrutes()->sum('minutos_solicitados');
                                $restantes = $minutos_computo - $minutos_disfrutados;
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
                                $restantes_formateados = sprintf('%02d:%02d', intdiv(abs($restantes), 60), abs($restantes) % 60);
                                if ($restantes < 0) {
                                    $restantes_formateados = '-' . $restantes_formateados;
                                }

                                return $restantes_formateados;
                            }),
                    ])

            ]);
    }
}
