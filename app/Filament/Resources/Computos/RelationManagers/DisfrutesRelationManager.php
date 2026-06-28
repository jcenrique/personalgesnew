<?php

namespace App\Filament\Resources\Computos\RelationManagers;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Computos\ComputoResource;
use App\Filament\Resources\Computos\RelationManagers\Actions\AprobarDisfrute;
use App\Filament\Resources\Computos\RelationManagers\Actions\RechazarDisfrute;
use App\Models\User;
use App\Notifications\NotificacionSolicitudDiaComputo;
use Asmit\ResizedColumn\HasResizableColumn;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class DisfrutesRelationManager extends RelationManager
{
    use HasResizableColumn;

    protected static string $relationship = 'disfrutes';

    protected static ?string $relatedResource = ComputoResource::class;

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->dateTime('d F Y')
                    ->color('primary'),
                TextColumn::make('minutos_solicitados')
                    ->label(__('Horas solicitadas'))
                    ->color('danger')
                    ->formatStateUsing(function ($record) {
                        $minutos = $record->minutos_solicitados;

                        $horas = intdiv($minutos, 60);

                        $mins = $minutos % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

                TextColumn::make('status')
                    ->label(__('Estado')),
            ])
            ->filters([])
            ->recordActions([
                // hay que crear 3 acciones aprobar, rechazar y editar
                AprobarDisfrute::make('aprobar_disfrute')
                    ->hidden(fn ($record) => $record->status !== StatusSolicitudes::Solicitado),
                RechazarDisfrute::make('rechazar_disfrute')

                    ->hidden(fn ($record) => $record->status !== StatusSolicitudes::Solicitado),

            ])
            ->headerActions([

                // crear una accion que permita crear un nuevo disfrute asociado al computo, esta accion debe abrir un modal con un formulario para crear el disfrute, el formulario debe tener los campos fecha_disfrute y minutos_solicitados, ademas debe tener un campo oculto para asociar el disfrute al computo actual, este campo oculto debe tener el valor del id del computo actual, para esto se puede usar el metodo getRecord() para obtener el registro actual del computo y luego obtener su id, finalmente se debe agregar la accion al headerActions
                Action::make('nuevo_disfrute')
                    ->label(__('Agregar disfrute'))
                    ->modalHeading(__('Agregar nuevo disfrute'))
                    ->modalWidth(Width::Small)
                    ->schema([
                        DatePicker::make('fecha_disfrute')
                            ->label(__('Fecha de disfrute'))
                            ->required()
                            ->native(false)
                            ->suffixIcon('heroicon-o-calendar')
                            ->locale('es')
                            ->format('Y-m-d')
                            ->displayFormat('d F Y')
                            ->closeOnDateSelection()
                            ->disabledDates(function () {
                                $start = Carbon::now()->addYear(-10)->startOfMonth();
                                $end = Carbon::now();

                                $period = CarbonPeriod::create($start, $end);
                                $disabled = [];

                                foreach ($period as $date) {
                                    // desabilitar fechas anteriores a la actual
                                    if ($date->isPast()) {
                                        $disabled[] = $date->translatedFormat('Y-m-d');
                                    }
                                }
                                // desabilitar fechas ya solicitadas
                                $diasSolicitados = $this->getOwnerRecord()->user->disfrutes()->get();

                                foreach ($diasSolicitados as $dia) {

                                    $disabled[] = Carbon::parse($dia->fecha_disfrute)->translatedFormat('Y-m-d');
                                }

                                return $disabled;
                            }),

                        TimePicker::make('minutos_solicitados')
                            ->label(__('Horas solicitadas'))
                            ->required()
                            ->native(false)
                            // limitar maximo de horas a solicitar a 9 horas
                            ->maxDate(Carbon::createFromTime(9, 0, 0))

                            ->suffixIcon('heroicon-o-clock')
                            ->seconds(false)
                            ->locale('es')
                            ->format('H:i')
                            ->displayFormat('H:i'),

                    ])
                    ->action(function ($data, $livewire) {
                        $computo = $this->getOwnerRecord();
                        $minutos_solicitados = (Carbon::parse($data['minutos_solicitados'])->hour * 60) + (Carbon::parse($data['minutos_solicitados'])->minute);
                        // antes de crear el disfrute, verificar que el usuario tenga minutos disponibles para solicitar el día de computo, para esto se debe restar los minutos solicitados a los minutos disponibles del computo, si el resultado es negativo, mostrar una notificación de error y no crear el disfrute, si el resultado es positivo o cero, crear el disfrute normalmente
                        // para poder solicitar minimo debes disponer de la menos 3 hora 30 minutos para solicitar un día de computo, esto es para evitar que los usuarios soliciten días de computo sin tener suficientes minutos disponibles, para esto se puede hacer una validación antes de crear el disfrute, si el usuario no tiene al menos 30 minutos disponibles, mostrar una notificación de error y no crear el disfrute

                        $min_disponibles = $this->getOwnerRecord()->disponible;
                        $min_solicitados = $this->getOwnerRecord()->disfrutes()->sum('minutos_solicitados');
                        $restantes = $min_disponibles - $min_solicitados - $minutos_solicitados;
                        if ($restantes < -210) {
                            Notification::make()
                                ->title(__('No tienes suficientes minutos disponibles'))
                                ->body(__('Dispones de :minutos minutos disponibles, has excedido el límite de solicitud de días de computo. Para solicitar un día de computo debes disponer de al menos 3 horas y 30 minutos disponibles.'))
                                ->icon('heroicon-o-x-circle')
                                ->danger()
                                ->send();

                            return;
                        }

                        $dia_solicitado = $computo->disfrutes()->create([
                            'user_id' => $this->getOwnerRecord()->user_id,
                            'fecha_disfrute' => $data['fecha_disfrute'],
                            'minutos_solicitados' => Carbon::parse($data['minutos_solicitados'])->hour * 60 + Carbon::parse($data['minutos_solicitados'])->minute,
                            'status' => StatusSolicitudes::Solicitado,
                        ]);

                        Notification::make()
                            ->title(__('Solicitud enviada'))
                            ->icon('heroicon-o-document-text')

                            ->success()
                            ->send();
                        // notificar al admin por correo

                        $this->getOwnerRecord()->refresh();
                        $admins = User::where('role', 'super_admin')->get();
                        Notification::make()
                            ->title(__('Nuevo día de computo solicitado'))
                            ->body(__('Nuevo día de computo solicitado por :user para el :fecha_para_disfrute.', ['user' => $this->getOwnerRecord()->user->name, 'fecha_para_disfrute' => Carbon::parse($data['fecha_disfrute'])->translatedFormat('d F Y')]))
                            ->success()
                            ->sendToDatabase($admins);
                        FacadesNotification::send($admins, new NotificacionSolicitudDiaComputo($dia_solicitado, $this->getOwnerRecord()));
                        // actualiza el registro del computo para reflejar el nuevo número de minutos disponibles, esto se puede hacer refrescando el registro del computo para que se vuelva a calcular el número de minutos disponibles con la nueva solicitud de día de computo

                        // actulaizar el sidebar para reflejar el nuevo número de minutos disponibles
                        $livewire->dispatch('refresh-sidebar');

                        $livewire->dispatch('updateData', $data); // enviar un evento a la infolist para que se actualice el número de minutos disponibles en el sidebar
                    }),
            ]);
    }
}
