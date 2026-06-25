<?php

namespace App\Filament\Resources\Additionaldays\Pages;

use App\Filament\Resources\Additionaldays\AdditionaldayResource;
use App\Models\Additionalday;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AddAdittionaldays extends Page implements HasForms, HasTable


{

    use InteractsWithTable;
    use InteractsWithForms;
     use HasResizableColumn;

    public array $cantidades = [];

    protected static string $resource = AdditionaldayResource::class;

    protected string $view = 'filament.resources.additionaldays.pages.add-adittionaldays';

    //mostrar una tabla con todos los usuarios a excepcion de los superadministradores
    // la tabla debe contener una columna con el nombre de usuario y una columna con un spiner de incremento de dias adicionales
    //este spinner contendrá la cuenta de los dias adiccionales del usuario por año seleccionado
    //si no existen datos del año todo a cero


    public function getTitle(): string
    {
        $value = data_get($this->tableFilters, 'year.value');

        $year = (is_numeric($value) && $value > 0)
            ? (int) $value
            : now()->year;

        return "Añadir días adicionales - {$year}";
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
                    //validar contenido
                    $fila1 = array_map(fn($h) => strtolower(trim($h)), $rows[1]);

                    if ($fila1[0] === '' || $fila1[1] === '') {

                        Notification::make()
                            ->title('Error en el Excel')
                            ->body(__('No hay datos para importar'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);

                   //validar encabezado

                    if ($header[0] !== 'codigo_agente' || $header[1] !== 'dias') {

                        Notification::make()
                            ->title('Error en el Excel')
                            ->body(__('El formato de cabecera no es correcto'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $rowsCount = count($rows);
                    $protectedIds = [];


                    DB::transaction(function () use ($rows, $year, &$protectedIds) {

                        // 1️⃣ Obtener IDs que NO se deben borrar

                        $protectedAdditionalDayIds =
                            DB::table('disfrutes')
                            ->where('disfrutable_type', Additionalday::class)
                            ->whereIn('disfrutable_id', function ($query) use ($year) {
                                $query->select('id')
                                    ->from('additionaldays')
                                    ->where('year', $year);
                            })
                            ->pluck('disfrutable_id')
                            ->unique();


                        $protectedIds =  $protectedAdditionalDayIds;


                        // 2️⃣ Borrado selectivo

                        Additionalday::where('year', $year)
                            ->whereNotIn('id', $protectedAdditionalDayIds)
                            ->delete();

                        // 3️⃣ Procesar Excel
                        foreach ($rows as $index => $row) {

                            if ($index === 0) continue; // cabecera

                            [$codigo, $dias] = $row;

                            if (! $codigo) continue;

                            $user = User::where('codigo_agente', $codigo)->first();

                            if (! $user) continue;

                            $dias = (int) $dias;

                            // 4️⃣ Restar lo ya disfrutado

                            $disfrutados = DB::table('disfrutes')
                                ->where('disfrutable_type', Additionalday::class)
                                ->whereIn('disfrutable_id', function ($query) use ($user, $year) {
                                    $query->select('id')
                                        ->from('additionaldays')
                                        ->where('user_id', $user->id)
                                        ->where('year', $year);
                                })
                                ->count();


                            $diasFinal = max(0, $dias - $disfrutados);

                            // 5️⃣ Insertar registros
                            if ($diasFinal > 0) {

                                Additionalday::insert(
                                    collect(range(1, $diasFinal))->map(fn() => [
                                        'user_id' => $user->id,
                                        'year' => $year,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ])->toArray()
                                );
                            }
                        }
                    });


                    $protected = Additionalday::with('user')
                        ->whereIn('id', $protectedIds)
                        ->get();

                    $report = $protected->map(function ($day) use ($year, $rows) {
                        return [
                            'codigo_agente' => $day->user->codigo_agente ?? null,

                            'name' => $day->user->name,

                            'dia_disfrute' => $day->disfrute->fecha_disfrute->translatedFormat('d/M/Y'),


                        ];
                    });
                    //guardar en cache
                    $reportId = uniqid();

                    cache()->put("report_$reportId", $report, now()->addMinutes(10));



                    Notification::make()
                        ->title(__('Importación completada'))
                        ->persistent()
                        ->body(
                            count($protected) > 0
                                ? __('Alguno de los dias adicionales no se han eliminado por estar ya concedidos al agente')
                                . ':<br><strong>' . $report->pluck('name')->unique()
                                ->map(fn($name) => ucwords(str_replace('_', ' ', $name)))
                                ->implode('</strong><br><strong>') .
                                '</strong><br>'
                                : __('Importación completada sin incidencias')
                        )
                        ->actions([
                            //fn () => $this->exportNoEliminados($report->toArray())
                            Action::make('excel_export')
                                ->button()
                                ->icon('fas-file-export')
                                ->label(__('Exportar a Excel'))

                                ->url(url("/export-no-eliminados/{$reportId}/{$year}/{$rowsCount}"))
                                ->openUrlInNewTab()



                                ->color('primary'),

                        ])
                        ->success()
                        ->send();
                })

        ];
    }

private function isRowEmpty(array $row): bool
{
    foreach ($row as $cell) {
        if (trim((string) $cell) !== '') {
            return false;
        }
    }
    return true;
}


    public static function table(Table $table): Table
    {
        return $table
            ->query(User::orderByRaw('codigo_agente + 0 ASC'))

            ->columns([
                TextColumn::make('codigo_agente')
                    ->label(__('Código agente'))
                    ->extraAttributes(['style' => 'height: 50px;'])
                    ->numeric()

                    ->searchable(),
                TextColumn::make('name')
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
                            ->map(fn($name) => ucwords(str_replace('_', ' ', $name)))
                            ->implode(', ');
                    })
                    ->badge()
                    ->separator(', '),

                TextInputColumn::make('cantidad')
                    ->width('50px')
                    ->label(__('Días'))
                    ->type('number')
                    ->rules(['integer', 'min:0', 'max:3'])
                    ->default(0)

                    ->step('1')



                    ->getStateUsing(function ($record, $livewire) {

                        $year = $livewire->tableFilters['year']['value'] ?: now()->year;

                        return Additionalday::where('user_id', $record->id)
                            ->where('year', $year)
                            ->count();
                    })


                    ->updateStateUsing(function ($record, $state, $livewire) {


                        $year = $livewire->tableFilters['year']['value'] ?: now()->year;

                        $newCount = (int) $state;

                        $query = Additionalday::where('user_id', $record->id)
                            ->where('year', $year);

                        $currentCount = $query->count();

                        if ($newCount > $currentCount) {
                            Additionalday::insert(
                                collect(range(1, $newCount - $currentCount))->map(fn() => [
                                    'user_id' => $record->id,
                                    'year' => $year,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])->toArray()
                            );
                        }

                        if ($newCount < $currentCount) {
                            $query->latest()->take($currentCount - $newCount)->delete();
                        }

                        return $newCount;
                    })


            ])
            ->recordActions([])
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




                //crear un filtro para usuarios
                SelectFilter::make('id')
                    ->label(__('Usuario'))
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable(),

            ]);
    }
}
