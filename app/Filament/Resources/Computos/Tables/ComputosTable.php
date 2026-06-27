<?php

namespace App\Filament\Resources\Computos\Tables;

use App\Models\Disfrute;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class ComputosTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('year')
                    ->label(__('Año'))
                    ->badge()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('User'))

                    ->searchable()

                    ->sortable(),
                TextColumn::make('disponible')
                    ->label(__('Computo'))
                    ->summarize(
                        Sum::make()
                            ->label(__('Total computos'))
                            ->formatStateUsing(function ($state) {
                                $horas = intdiv($state, 60);
                                $mins  = $state % 60;
                                return sprintf('%02d:%02d', $horas, $mins);
                            })
                    )

                    ->formatStateUsing(function ($record) {
                        $minutos = $record->disponible;

                        $horas = intdiv($minutos, 60);

                        $mins  = $minutos % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('disfrutes.minutos_solicitados')

                    ->label(__('Solicitado'))
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
                    })
                    ->summarize(
                        Sum::make()
                            ->label(__('Total Solicitados'))
                            ->extraAttributes(['class' => 'text-red-600'])
                            ->formatStateUsing(function ($state) {
                                $horas = intdiv($state, 60);
                                $mins  = $state % 60;
                                return sprintf('%02d:%02d', $horas, $mins);
                            })
                    ),



                TextColumn::make('pendientes')
                    ->label(__('Disponible'))
                    ->color(function ($record) {
                        $minutos_computo = $record->disponible;
                        $minutos_disfrutados = $record->disfrutes()->sum('minutos_solicitados');
                       $restantes = $minutos_computo - $minutos_disfrutados;
                        if($restantes<0){
                            return 'danger';
                        } elseif($restantes>0){
                            return 'success';
                        } else {
                            return 'warning';
                        }
                    })
                    ->state(function ($record) {
                        $minutos_computo = $record->disponible;
                        $minutos_disfrutados = $record->disfrutes()->sum('minutos_solicitados');
                        return $minutos_computo - $minutos_disfrutados;
                    })
                    ->formatStateUsing(function ($state) {
                         //si el total es negativo, mostrar mostrar el valor con signo negativo delante y el valor absoluto del tiempo, para evitar confusiones al mostrar tiempos negativos
                         if ($state < 0) {
                            $horas =abs( intdiv($state, 60));
                            $mins = abs($state % 60);
                            return '-' . sprintf('%02d:%02d', $horas, $mins);
                        }

                        $horas = intdiv($state, 60);
                        $mins = $state % 60;


                        return sprintf('%02d:%02d', $horas, $mins);
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label(__('Total disponibles'))
                            ->extraAttributes(['class' => 'text-green-600'])
                            ->using(function (Builder $query): string {

                                $total = $query->get()->sum(function ($record) {
                                    $difrute_sum = Disfrute::where('disfrutable_id', $record->id)->where('disfrutable_type', 'App\Models\Computo')->sum('minutos_solicitados');

                                    $minutos_computo = $record->disponible;
                                    $total = $minutos_computo - $difrute_sum;
                                    return $total;
                                });



                                $horas = intdiv($total, 60);
                                $mins = $total % 60;

                                return sprintf('%02d:%02d', $horas, $mins);
                            })
                    ),



                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

                // //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->preload(true)
                    //mostrar una lista de años, en los que se dispone de día adicional
                    ->options(function () {
                        $years =  DB::table('computos')->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    ->default(date('Y'))
                    ->searchable()
                    ->placeholder(__('Selecciona un año')),

                SelectFilter::make('user_id')
                    ->label(__('Usuario'))
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable(),


            ])
            ->recordActions([
                ViewAction::make()->hiddenLabel(true)->tooltip(__('Ver'))->color('primary'),


                EditAction::make()
                    ->hiddenLabel(true)
                    ->tooltip(__('Edit'))
                    ->color('success')
                    ->modalWidth(Width::Small)
                    ->mutateRecordDataUsing(function (array $data,  $record): array {

                        $horas = intdiv($record->disponible, 60);
                        $minutos = $record->disponible % 60;

                        $data['horas'] = $horas;
                        $data['minutos'] = $minutos;
                        return $data;
                    })
                    ->action(function ($data, EditAction $action, $record) {

                        //comvertir los campos horas y minutos a minutos para guardar en la DB

                        $minutos_computo = ($data['horas'] * 60) + $data['minutos'];


                        $record->update([

                            'year' => $data['year'],
                            'disponible' => $minutos_computo,

                        ]);
                    }),
                DeleteAction::make()->hiddenLabel(true)->tooltip(__('Delete'))->color('danger'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                ]),
            ]);
    }
}
