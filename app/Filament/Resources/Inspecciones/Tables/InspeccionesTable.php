<?php

namespace App\Filament\Resources\Inspecciones\Tables;

use App\Filament\Tables\Columns\BienMalColumn;
use App\Models\User;
use App\Models\Zona;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InspeccionesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_hora', 'desc')
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->sortable()
                    ->label(__('Tipo Inspección'))
                    ->color(function ($record) {
                        if ($record->type == 'periodica') {
                            return 'info';
                        } else {
                            return 'success';
                        }
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->type == 'periodica') {
                            return __('Periódica');
                        } else {
                            return __('Especial');
                        }

                        return '';
                    }),
                TextColumn::make('estacion.zona.name')
                    ->sortable()
                    ->weight(FontWeight::ExtraBold)
                    ->label(__('Zona')),

                TextColumn::make('estacion.name')
                    ->sortable()
                    ->weight(FontWeight::ExtraBold)
                    ->label(__('Estacion')),

                TextColumn::make('fecha_hora')
                    ->dateTime('d F Y H:m')
                    ->sortable()
                    ->label(__('Fecha y Hora')),

                TextColumn::make('user1.name')
                    ->label(__('Jefe de Servicio')),

                TextColumn::make('user2.name')
                    ->label(__('Técnico'))
                    ->description(function ($record) {

                        $user = User::find($record->user_id_2);
                        $roles = $user->roles()
                            ->pluck('name')
                            ->map(fn ($role) => str_replace('_', ' ', ucwords($role)))
                            ->join(', ');

                        return $roles;
                    }),

                BienMalColumn::make('resultados_ok_ko')
                    ->label(__('Resultados bien/mal'))
                    ->width('150px')
                    ->alignCenter()
                    ->wrapHeader()
                    ->getStateUsing(function ($record) {

                        $count_ok = $record->resultados->where('resultado', 1)->count();
                        $count_ko = $record->resultados->where('resultado', 0)->count();

                        return $count_ok.' / '.$count_ko;
                    }),

            ])
            ->filters([
                // filtra por zona, pero el filtro tiene que ser un select con las zonas disponibles
                // el filtro debe mostrar las zonas disponibles y al seleccionar una zona, debe mostrar solo las inspecciones de las estaciones que pertenecen a esa zona

                SelectFilter::make('estaciones.zona_id')
                    ->label(__('Zona'))
                    ->options(function () {

                        return Zona::with('estaciones')->orderBy('name')->get()->pluck('name', 'id');
                    })
                    ->query(function (Builder $query, array $data) {
                        if (! $data['value']) {
                            return;
                        }

                        $query->whereHas('estacion', function (Builder $query) use ($data) {
                            $query->where('zona_id', $data['value']);
                        });
                    })
                    ->searchable(),

                SelectFilter::make('type')
                    ->label(__('Tipo Inspeción'))
                    ->options([
                        'periodica' => __('Periódica'),
                        'especial' => __('Especial'),
                    ])
                    ->default('periodica'),
                SelectFilter::make('cuatrimestre')
                    ->indicator(__('Período'))

                    ->label(__('Cuatrimestre'))
                    ->options(function () {
                        $current = now()->year;
                        $years = range($current, $current - 3);

                        $opts = [];

                        foreach ($years as $year) {
                            $opts[$year] = [
                                "{$year}-1" => __('1º Cuatrimestre'),
                                "{$year}-2" => __('2º Cuatrimestre'),
                                "{$year}-3" => __('3º Cuatrimestre'),

                            ];
                        }

                        return $opts;
                    })
                    ->default(self::cuatrimestreActual())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if (! $value || ! str_contains($value, '-')) {
                            return $query;
                        }

                        [$year, $cuatri] = explode('-', $value, 2);

                        $ranges = [
                            1 => ['start' => "$year-01-01", 'end' => "$year-04-30"],
                            2 => ['start' => "$year-05-01", 'end' => "$year-08-31"],
                            3 => ['start' => "$year-09-01", 'end' => "$year-12-31"],

                        ];

                        return $query
                            ->whereDate('fecha_hora', '>=', $ranges[$cuatri]['start'])
                            ->whereDate('fecha_hora', '<=', $ranges[$cuatri]['end']);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Edit')),
                DeleteAction::make()
                    ->hiddenLabel()
                    ->tooltip(__('Delete')),
                Action::make('export_pdf')
                    ->hiddenLabel(true)
                    ->hidden(function ($record) {
                        return $record->type == 'especial';
                    })
                    ->color('info')
                    ->tooltip('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('inspecciones.export-pdf', ['inspeccion' => $record]))
                    ->openUrlInNewTab(),

                Action::make('pdfEspecial')
                    ->hiddenLabel(true)
                    ->tooltip('Descargar PDF Inspección Especial')
                    ->color('success')
                    ->hidden(function ($record) {
                        return $record->type == 'periodica';
                    })
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn ($record) => route('inspecciones.export-especial-pdf', ['inspeccion' => $record]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function cuatrimestreActual(): string
    {
        $year = now()->year;
        $month = now()->month;

        if ($month <= 4) {
            return "{$year}-1";
        } elseif ($month <= 8) {
            return "{$year}-2";
        } elseif ($month <= 12) {
            return "{$year}-3";
        }

        return "{$year}-4"; // si usas 4 periodos
    }
}
