<?php

namespace App\Filament\Widgets;

use App\Enum\StatusSolicitudes;
use App\Filament\Actions\AprobarDisfruteAction;
use App\Filament\Actions\RechazarDisfruteAction;
use App\Models\Disfrute;
use App\Models\Reconocimiento;
use Asmit\ResizedColumn\HasResizableColumn;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkActionGroup;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Resumen extends TableWidget implements HasSchemas
{
    use InteractsWithSchemas;



    use HasWidgetShield;
    use HasResizableColumn;


    protected string $view = 'filament.widgets.resumen';


    public function reconocimiento(Schema $schema): Schema
    {
        $ultimoReconocimiento = Reconocimiento::where('user_id', Auth::id())->latest()->first();

        return $schema
            ->record($ultimoReconocimiento)
            ->components([

                TextEntry::make('fecha')
                    ->label(__('Último reconocimiento médico'))
                    // ->hiddenLabel(true)
                    ->maxWidth(Width::Full)
                    ->getStateUsing(function ($record) {
                        if ($record) {
                            return $record->fecha->translatedFormat('d F Y');
                        }
                        return __('No hay registros');
                    })
                    ->size(TextSize::Large)
                    ->color('success')
                    ->badge()
                    ->icon(Heroicon::Calendar)
                    ->fontFamily(FontFamily::Mono),
            ]);
    }


    protected function getTableHeading(): string | Htmlable | null
    {
        return __('Solicitudes pendientes');
    }

    public function getColumnSpan(): int | string | array
    {
        return 'full';
    }


    public function table(Table $table): Table
    {
        return $table
            // ->query(fn(): Builder => Disfrute::query()->where('status' , StatusSolicitudes::Solicitado))
            ->query(function (): Builder {
                $user = auth()->user();
                $query = Disfrute::query()->where('status', StatusSolicitudes::Solicitado);

                // filtrar por zonas del usuario autenticado
                $zonaIds = $user->zonas()
                    ->pluck('zonas.id')
                    ->toArray();

                if (! empty($zonaIds)) {
                    $query->whereHas('user.residencias', function (Builder $q) use ($zonaIds) {
                        $q->whereIn('zona_id', $zonaIds);
                    });
                }

                return $query;
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('User'))
                    ->numeric()
                    ->sortable(),

                TextColumn::make('disfrutable_type')
                    ->label(__('Tipo de recurso solicitado'))
                    ->getStateUsing(function ($record) {

                        if ($record->disfrutable_type === 'App\Models\Sabado') {
                            return __('Sábado');
                        } else if ($record->disfrutable_type === 'App\Models\Additionalday') {
                            return __('Día adicional');
                        } else if ($record->disfrutable_type === 'App\Models\Companyday') {
                            return __('Día solicitado por la empresa');
                        }
                        // Agrega más condiciones para otros tipos de recursos si es necesario
                        return $record->disfrutable_type;
                    })
                    //mostrar la fecha de sabado  asociada si el recurso es un sabado, utilizando la relación entre rechazo y sábado para obtener la fecha de sabado trabajado asociada al rechazo
                    ->description(function ($record) {

                        if ($record->disfrutable_type === 'App\Models\Sabado') {
                            return  $record->disfrutable()->latest()->first()->sabado_trabajado?->translatedFormat('d F Y');
                        } else if ($record->disfrutable_type === 'App\Models\Additionalday') {
                            return  $record->disfrutable()->latest()->first()->year;
                        } else if ($record->disfrutable_type === 'App\Models\Companyday') {
                            return  $record->disfrutable()->latest()->first()->fecha?->translatedFormat('d F Y');
                        }
                        // Agrega más condiciones para otros tipos de recursos si es necesario
                        return '';
                    }),


                // TextColumn::make('disfrutable_type')
                //     ->searchable(),

                TextColumn::make('fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('minutos_solicitados')
                    ->placeholder(__('No procede'))
                    ->label(__('Horas solicitadas'))
                    ->getStateUsing(function ($record) {
                        $horas = intdiv($record->minutos_solicitados, 60);
                        $mins = $record->minutos_solicitados % 60;
                        //si el tipo es computo mostrar formato horas si no vacio
                        if ($record->disfrutable_type === 'App\Models\Computo') {
                            return sprintf('%02d:%02d', $horas, $mins);
                        }
                        return null;
                    })

                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Estado'))
                    ->badge(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //poner acciones de aprobar y denegar

                AprobarDisfruteAction::make('aprobar')
                    ->hiddenLabel(true),
                RechazarDisfruteAction::make('rechazar')
                    ->hiddenLabel(true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
