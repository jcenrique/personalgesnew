<?php

namespace App\Filament\Resources\Companydays\Tables;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Companydays\Actions\AprobarCompanydayAction;
use App\Filament\Resources\Companydays\Actions\RechazarCompanydayAction;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;

class CompanydaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('user.name')
                    ->label(__('Usuario'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label(__('Fecha'))
                    ->date('d F Y')

                    ->sortable(),
                TextColumn::make('razon')
                    ->label(__('Razón'))
                    ->searchable(),
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
                    ->options(function (): array {
                        $user = Auth::user();

                        // Si es super_admin o admin, mostrar todos los usuarios
                        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
                            return User::orderBy('name')->pluck('name', 'id')->toArray();
                        }

                        // Si NO es admin, obtener zonas del usuario autenticado directamente
                        $zonaIds = $user->zonas()
                            ->pluck('id')
                            ->toArray();

                        // Solo mostrar usuarios de esas zonas
                        return User::whereHas('zonas', function (Builder $q) use ($zonaIds) {
                            $q->whereIn('id', $zonaIds);
                        })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),

            ])
            ->recordActions([
                AprobarCompanydayAction::make('aprobar')
                    ->visible(fn ($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),
                RechazarCompanydayAction::make('rechazar')
                    ->visible(fn ($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),
                EditAction::make()

                    ->modalWidth(Width::Small)
                    ->mutateRecordDataUsing(function (array $data, $record) {

                        if ($record->disfrute) {
                            $data['disfrute']['fecha_disfrute'] = $record->disfrute->fecha_disfrute;
                            $data['disfrute']['status'] = $record->disfrute->status;
                        } else {

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
                            StatusSolicitudes::Rechazado,
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

                        $record->save(); // dispara updated() en Additionalday
                    }),

                DeleteAction::make()
                    ->hiddenLabel(true)
                    ->modalHeading(__('¿Eliminar  día?'))
                    ->modalDescription(__('¿Estás seguro de que deseas eliminar este día? Esta acción no se puede deshacer, pero puedes restaurarla desde la pestaña de eliminados si es necesario.'))
                    ->tooltip(__('Delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
