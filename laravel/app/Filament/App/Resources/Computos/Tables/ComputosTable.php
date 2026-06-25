<?php

namespace App\Filament\App\Resources\Computos\Tables;

use App\Filament\App\Resources\Computos\Actions\SolicitarDíaComputo;
use App\Filament\App\Resources\Computos\Actions\SolicitarDíaComputoRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComputosTable
{
    public static function configure(Table $table): Table
    {
        return $table


            ->columns([
                TextColumn::make('year')
                    ->label(__('Año'))
                    ->color('info')

                    ->sortable(),

                TextColumn::make('disponible')
                    ->label(__('Computo'))
                    ->formatStateUsing(function ($record) {
                        $minutos = $record->disponible;

                        $horas = intdiv($minutos, 60);

                        $mins  = $minutos % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('pendientes')
                    ->label(__('Disponible'))
                    ->color('success')
                    ->getStateUsing(function ($record) {

                        if ($record->disfrutes()->exists()) {


                            $minutos_computo = $record->disponible;
                            $minutos_disfrutados =   $minutos = $record->disfrutes()->sum('minutos_solicitados');
                            $minutos = $minutos_computo - $minutos_disfrutados;
                            //si el total es negativo, mostrar mostrar el valor con signo negativo delante y el valor absoluto del tiempo, para evitar confusiones al mostrar tiempos negativos
                            if ($minutos < 0) {
                                $horas = abs(intdiv($minutos, 60));
                                $mins = abs($minutos % 60);
                                return '-' . sprintf('%02d:%02d', $horas, $mins);
                            }
                            $horas = intdiv($minutos, 60);

                            $mins  = $minutos % 60;

                            return sprintf('%02d:%02d', $horas, $mins);
                        }
                        $minutos_computo = $record->disponible;

                        $horas = intdiv($minutos_computo, 60);

                        $mins  = $minutos_computo % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('disfrutado')
                    ->label(__('Disfrutado'))
                    ->color('danger')
                    ->getStateUsing(function ($record) {

                        if ($record->disfrutes()->exists()) {
                            $minutos = $record->disfrutes()->sum('minutos_solicitados');

                            $horas = intdiv($minutos, 60);

                            $mins  = $minutos % 60;

                            return sprintf('%02d:%02d', $horas, $mins);
                        }


                        $horas = 0;

                        $mins  = 0;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('dias_disfrutados')
                    ->label(__('Dias disfrutados'))
                    ->color('danger')
                    ->getStateUsing(function ($record) {

                        if ($record->disfrutes()->exists()) {
                            $dias = $record->disfrutes()->count();


                            return $dias;
                        }


                        $horas = 0;

                        $mins  = 0;

                        return sprintf('%02d:%02d', $horas, $mins);
                    })


            ])
            ->filters([
                // //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->preload(true)
                    //mostrar una lista de años, en los que se dispone de día adicional
                    ->options(function () {
                        $years =  DB::table('computos')->where('user_id', Auth::id())->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    ->default(date('Y'))
                    ->searchable()
                    ->placeholder(__('Selecciona un año')),
            ])

            ->recordActions([
                SolicitarDíaComputoRecord::make('solicitar')

                    ->visible(function ($record) {

                        $restante = $record->disponible - $record->disfrutes()->sum('minutos_solicitados');
                        //habilitar el boton si el computo tiene mas de 3:30 horas disponibles
                        return !($restante < 210) && $record->year == date('Y'); // 3 horas y 30 minutos en minutos
                    }),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->toolbarActions([
                BulkActionGroup::make([
                    //  DeleteBulkAction::make(),

                ]),
            ]);
    }
}
