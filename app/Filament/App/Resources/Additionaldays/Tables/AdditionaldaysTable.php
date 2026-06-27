<?php

namespace App\Filament\App\Resources\Additionaldays\Tables;

use App\Enum\StatusSolicitudes;
use App\Filament\App\Resources\Additionaldays\Actions\SolicitarAdditionalday;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdditionaldaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ->poll(function ($livewire) {


            //     $livewire->dispatch('refresh-sidebar');
            //     return '10s';
            // })
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('year' , 'asc')
                    ->withAggregate('disfrute', 'status')
                    ->orderBy('disfrute_status', 'desc')
                    ->withAggregate('disfrute', 'fecha_disfrute')

                    ->orderBy('disfrute_fecha_disfrute' , 'desc');
            })
            ->columns([

                TextColumn::make('year')
                    ->label(__('Año'))
                    ->color('info')
                    ->sortable(),

                TextColumn::make('disfrute.status')
                    ->label(__('Estado'))
                    ->default(StatusSolicitudes::Disponible)
                    ->sortable()
                    ->badge(),

                TextColumn::make('disfrute.fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->placeholder(StatusSolicitudes::Disponible->getLabel())
                    ->color('success')
                    ->weight(FontWeight::ExtraBold)
                    ->date('d F Y')
                    ->sortable(),



            ])

            ->filters([
                //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->preload(true)
                    //mostrar una lista de años, en los que se dispone de día adicional
                    ->options(function () {
                        $years =  DB::table('additionaldays')->where('user_id', Auth::id())->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    //por defecto año actual
                    ->default(date('Y'))
                    ->searchable()
                    ->placeholder(__('Selecciona un año')),

            ])
            ->recordActions([
                //EditAction::make(),
                SolicitarAdditionalday::make()
                    ->visible(
                        function ($record, $livewire) {


                            return $record->disfrute == null && ($livewire->activeTab === 'available' || $livewire->activeTab === 'all') && $record->year === now()->year;
                        }
                    )

            ])

            ->modifyQueryUsing(function ($query) {
                $query->where('user_id', Auth::id());
            })

            ->toolbarActions([
                BulkActionGroup::make([
                        DeleteBulkAction::make(),

                ]),
            ]);
    }
}
