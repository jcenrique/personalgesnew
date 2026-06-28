<?php

namespace App\Filament\Resources\Computos\Pages;

use App\Filament\Resources\Computos\ComputoResource;
use App\Models\Computo;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AddComputos extends Page implements HasForms, HasTable
{
    use HasResizableColumn;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = ComputoResource::class;

    protected string $view = 'filament.resources.computos.pages.add-computos';

    public function getTitle(): string
    {

        return __('Añadir computos');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importar')
                ->label(__('Importar de Excel'))
                ->modalWidth(Width::Small)
                ->schema([

                    FileUpload::make('file')
                        ->label(__('Archivo'))
                        ->required()
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']),

                    Select::make('year')
                        ->label(__('Año'))
                        ->options(function () {
                            $currentYear = date('Y');

                            return [
                                $currentYear - 2 => $currentYear - 2,
                                $currentYear - 1 => $currentYear - 1,
                                $currentYear => $currentYear,
                                $currentYear + 1 => $currentYear + 1,

                            ];
                        })
                        ->required(),
                ])
                ->action(function (array $data) {

                    $year = (int) $data['year'];

                    $rows = Excel::toArray([], $data['file'])[0];
                    // validar contenido
                    $fila1 = array_map(fn ($h) => strtolower(trim($h)), $rows[1]);

                    if ($fila1[0] === '' || $fila1[1] === '') {

                        Notification::make()
                            ->title('Error en el Excel')
                            ->body(__('No hay datos para importar'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $header = array_map(fn ($h) => strtolower(trim($h)), $rows[0]);

                    // validar encabezado

                    if ($header[0] !== 'codigo_agente' || $header[1] !== 'minutos') {

                        Notification::make()
                            ->title('Error en el Excel')
                            ->body(__('El formato de cabecera no es correcto'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $rowsCount = count($rows);

                    $reportData = [];

                    DB::transaction(function () use ($rows, $year, &$reportData) {

                        // guardar el computo nuevo y eliminar el antiguo solo si el usuario no ha disfrutado de horas del computo, si ha disfrutado se guarda el nuevo computo pero no se elimina el antiguo y se muestra un mensaje indicando que no se han eliminado algunos dias adicionales por estar ya disfrutados por el agente

                        // 3️⃣ Procesar Excel
                        foreach ($rows as $index => $row) {

                            if ($index === 0) {
                                continue;
                            } // cabecera

                            [$codigo, $minutos] = $row;

                            if (! $codigo) {
                                continue;
                            }

                            $user = User::where('codigo_agente', $codigo)->first();

                            if (! $user) {
                                continue;
                            }

                            $minutos = (int) $minutos;

                            // Verificar si el usuario tiene un cómputo para el año especificado si no tiene, se crea uno nuevo, si existe se modifica el actual, no se tiene en cuenta los disfutes asociados

                            $computo = Computo::where('user_id', $user->id)
                                ->where('year', $year)
                                ->first();
                            // si el usuario ha disfrutado de horas no se realizan cambios y se crea el informe de usuarios que no se han modificado con los dias disfrutados
                            if ($computo && $computo->disfrutes()->exists()) {
                                // se crea un informe de usuarios que no se han modificado con los dias disfrutados para exportar a excel posteriormente
                                $reportData[] = [
                                    'codigo_agente' => $user->codigo_agente,
                                    'name' => $user->name,

                                    'horas_minutos_disfrutados' => sprintf('%02d:%02d', intdiv($computo->disfrutes()->sum('minutos_solicitados'), 60), $computo->disfrutes()->sum('minutos_solicitados') % 60),
                                ];

                                continue;
                            }
                            if ($computo) {
                                $computo->update([
                                    'disponible' => $minutos,
                                ]);
                            } else {
                                Computo::create([
                                    'user_id' => $user->id,
                                    'year' => $year,
                                    'disponible' => $minutos,
                                ]);
                            }
                        }
                    });

                    $reportId = uniqid();

                    cache()->put("report_$reportId", $reportData, now()->addMinutes(10));

                    Notification::make()
                        ->title('Importación completada')
                        ->persistent()
                        ->body(
                            count($reportData) > 0
                                ? __('La importación se ha completado, pero algunos usuarios no se han modificado porque ya han disfrutado de horas del cómputo. Puedes exportar un informe con los usuarios que no se han modificado y las horas disfrutadas.')
                                : __('La importación se ha completado correctamente.')
                        )
                        ->actions([

                            Action::make('excel_export')
                                ->button()
                                ->icon('fas-file-export')
                                ->label(__('Exportar a Excel'))

                                ->url(url("/export-computo-no-modificados/{$reportId}/{$year}/{$rowsCount}"))
                                ->openUrlInNewTab()

                                ->color('primary'),

                        ])
                        ->success()
                        ->send();
                }),

        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(User::orderByRaw('codigo_agente + 0 ASC'))
            // ->poll('20s')
            ->columns([
                TextColumn::make('codigo_agente')
                    ->label(__('Código agente'))
                    ->sortable()
                    ->numeric()

                    ->searchable(),
                TextColumn::make('name')
                    ->sortable()
                    ->label(__('Name'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('Email address'))
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->getStateUsing(function ($record) {
                        $roles_user = $record->roles;

                        return $roles_user
                            ->pluck('name')

                            ->unique()
                            ->map(fn ($name) => ucwords(str_replace('_', ' ', $name)))
                            ->implode(', ');
                    })
                    ->badge()
                    ->separator(', '),

                TextColumn::make('computo_disponible')
                    ->width('50px')
                    ->label(__('Minutos disponibles'))

                    ->getStateUsing(function ($record, $livewire) {
                        $year = $livewire->tableFilters['year']['value'] ?: now()->year;
                        $computo = Computo::where('user_id', $record->id)
                            ->where('year', $year)
                            ->first();
                        if (! $computo) {
                            return '00:00';
                        }
                        $horas = intdiv($computo->disponible, 60);
                        $mins = $computo->disponible % 60;

                        return sprintf('%02d:%02d', $horas, $mins);
                    }),

            ])
            ->recordActions([
                Action::make(__('Crear/Modificar Cómputo'))
                    ->hiddenLabel()
                    ->tooltip(__('Crear o actualizar cómputo para este usuario'))
                    ->color('success')
                    ->icon('far-edit')
                    ->modalWidth(Width::ExtraSmall)
                    ->schema([

                        Section::make([
                            TextInput::make('horas')
                                ->extraAttributes(['style' => 'width: 100px;'])

                                ->label(__('Horas'))
                                ->default(function ($record, $livewire) {
                                    $year = $livewire->tableFilters['year']['value'] ?: now()->year;
                                    $computo = Computo::where('user_id', $record->id)
                                        ->where('year', $year)
                                        ->first();
                                    if ($computo) {
                                        return intdiv($computo->disponible, 60);
                                    }

                                    return 0;
                                })
                                ->columnSpan(1)
                                ->required()
                                ->minValue(0)
                                // ->suffixIcon('heroicon-o-clock')
                                ->numeric(),

                            TextInput::make('minutos')
                                ->extraAttributes(['style' => 'width: 100px;'])
                                ->label(__('Minutos'))
                                ->default(function ($record, $livewire) {
                                    $year = $livewire->tableFilters['year']['value'] ?: now()->year;
                                    $computo = Computo::where('user_id', $record->id)
                                        ->where('year', $year)
                                        ->first();
                                    if ($computo) {
                                        return $computo->disponible % 60;
                                    }

                                    return 0;
                                })
                                ->columnSpan(1)
                                ->maxValue(59)
                                ->required()
                                ->minValue(0)
                                // ->suffixIcon('heroicon-o-clock')
                                ->numeric(),

                        ], )->columns(2),
                    ])
                    ->action(function ($data, Action $action, $record, $livewire) {

                        // comvertir los campos horas y minutos a minutos para guardar en la DB

                        $minutos_computo = ($data['horas'] * 60) + $data['minutos'];

                        // obtener el año del filtro de la tabla
                        $year = $livewire->tableFilters['year']['value'] ?: now()->year;

                        // si el usuario ha disfrutado de horas del computo no se permite modificar el computo
                        if ($record->disfrutes()->exists()) {
                            Notification::make()
                                ->title(__('No se puede modificar el cómputo'))
                                ->body(__('El usuario ya ha disfrutado de horas del cómputo, por lo que no se puede modificar.'))
                                ->danger()
                                ->send();

                            return;
                        }
                        Computo::updateOrCreate(
                            [
                                'user_id' => $record->id,
                                'year' => $year,
                            ],
                            [
                                'disponible' => $minutos_computo,
                            ]
                        );
                    }),

            ])
            ->headerActions([])
            ->filters([

                SelectFilter::make('year')
                    ->label(__('Año'))
                    ->preload(true)
                    ->searchable()
                    ->default(now()->year)
                    ->placeholder(__('Selecciona un año'))
                    ->options(function () {
                        $currentYear = date('Y');

                        return [
                            $currentYear - 2 => $currentYear - 2,
                            $currentYear - 1 => $currentYear - 1,
                            $currentYear => $currentYear,
                            $currentYear + 1 => $currentYear + 1,

                        ];
                    })

                    ->query(function ($query, $data) {
                        // ✅ no hacemos nada -> no filtramos usuarios
                    }),

                // crear un filtro para usuarios
                SelectFilter::make('id')
                    ->label(__('Usuario'))
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),

            ]);
    }
}
