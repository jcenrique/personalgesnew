<?php

namespace App\Filament\Resources\Additionaldays\Tables;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Additionaldays\Actions\AprobarAdditionaldayAction;
use App\Filament\Resources\Additionaldays\Actions\RechazarAdditionadayAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdditionaldaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll(function ($livewire) {
                $livewire->dispatch('refresh-sidebar');
                return '10s';
            })
            ->defaultSort(function (Builder $query): Builder {
                return $query
                    ->orderBy('year', 'asc')
                    ->withAggregate('disfrute', 'status')
                    ->orderBy('disfrute_status', 'desc')
                    ->withAggregate('disfrute', 'fecha_disfrute')

                    ->orderBy('disfrute_fecha_disfrute', 'desc');
            })

            ->columns([
                TextColumn::make('year')
                    ->label(__('Año'))
                    ->color('info')

                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('Usuario'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('disfrute.status')
                    ->label(__('Estado'))
                    ->default(StatusSolicitudes::Disponible)
                    ->badge(),
                TextColumn::make('disfrute.fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->color('success')
                    ->weight(FontWeight::ExtraBold)
                    ->placeholder(StatusSolicitudes::Disponible->getLabel())
                    ->date('d F Y')
                    ->sortable(),


                TextColumn::make(('deleted_at'))
                    ->label(__('Eliminado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Creado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Actualizado en'))
                    ->dateTime('d F Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //filtro por año
                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->options(function () {
                        $years =  DB::table('additionaldays')->distinct()->orderBy('year', 'asc')->pluck('year', 'year')->toArray();
                        return $years;
                    })
                    //por defecto año actual
                    ->default(date('Y'))
                    ->searchable()
                    ->placeholder(__('Selecciona un año')),

                SelectFilter::make('user_id')
                    ->label(__('Usuario'))
                    ->options(function (): array {
                        $user = Auth::user();

                        // Si es super_admin o admin, mostrar todos los usuarios
                        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                            return \App\Models\User::orderBy('name')->pluck('name', 'id')->toArray();
                        }

                        // Si NO es admin, obtener zonas del usuario autenticado directamente
                        $zonaIds = $user->zonas()
                            ->pluck('id')
                            ->toArray();

                        // Solo mostrar usuarios de esas zonas
                        return \App\Models\User::whereHas('zonas', function (Builder $q) use ($zonaIds) {
                            $q->whereIn('id', $zonaIds);
                        })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),



            ])
            ->recordActions([
                AprobarAdditionaldayAction::make('aprobar')
                    ->visible(fn($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),
                RechazarAdditionadayAction::make('rechazar')
                    ->visible(fn($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),
                EditAction::make()


                    ->mutateRecordDataUsing(function (array $data, $record) {

                        if ($record->disfrute) {
                            $data['disfrute']['fecha_disfrute'] = $record->disfrute->fecha_disfrute;
                            $data['disfrute']['status'] = $record->disfrute->status;
                        }else{

                            $data['disfrute']['status'] = StatusSolicitudes::Disponible;
                        }
                        return $data;
                    })

                    ->hiddenLabel(true)
                    ->tooltip(__('Edit'))
                    ->action(function ($record, $data) {

                        $status = $data['disfrute']['status'];

                        // Si hay disfrute existente, lo cargamos como modelo
                        $disfrute = $record->disfrute;

                        if (in_array($status, [
                            StatusSolicitudes::Solicitado,
                            StatusSolicitudes::Aprobado,
                            StatusSolicitudes::Rechazado
                        ])) {

                            if ($disfrute) {
                                // ACTUALIZAR MODELO (dispara updated)
                                $disfrute->fill([
                                    'fecha_disfrute' => $data['disfrute']['fecha_disfrute'],
                                    'status' => $status,
                                ]);

                                $disfrute->save();
                            } else {
                                // CREAR MODELO (dispara created)
                                $record->disfrute()->create([
                                    'fecha_disfrute' => $data['disfrute']['fecha_disfrute'],
                                    'user_id' => $record->user_id,
                                    'status' => $status,
                                ]);
                            }
                        } elseif ($status === StatusSolicitudes::Disponible) {

                            if ($disfrute) {
                                // ELIMINAR MODELO (dispara deleted)
                                $disfrute->delete();
                            }
                        }

                        // Actualizar Additionalday
                        $record->year = $data['year'];
                        $record->save(); // dispara updated() en Additionalday
                    }),

                DeleteAction::make()
                    ->hiddenLabel(true)
                    ->modalHeading(__('¿Eliminar  día adicional?'))
                    ->modalDescription(__('¿Estás seguro de que deseas eliminar este día adicional? Esta acción no se puede deshacer, pero puedes restaurarla desde la pestaña de eliminados si es necesario.'))
                    ->tooltip(__('Delete')),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                ]),
            ]);
    }


}
