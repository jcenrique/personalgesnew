<?php

namespace App\Filament\App\Widgets;

use App\Enum\StatusSolicitudes;
use App\Models\Additionalday;
use App\Models\Companyday;
use App\Models\Computo;
use App\Models\Disfrute;
use App\Models\Reconocimiento;
use App\Models\Sabado;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ReconocimientoWidget extends Widget implements HasSchemas

{
    use InteractsWithSchemas;

    protected ?string $pollingInterval = '20s';


    protected static ?int $sort = 0;

    protected string $view = 'filament.app.widgets.reconocimiento-widget';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function getColumns(): int | array
    {
        return 4;
    }

    public function reconocimiento(Schema $schema): Schema
    {
        $ultimoReconocimiento = Reconocimiento::where('user_id', Auth::id())->latest()->first();

        return $schema
            ->record($ultimoReconocimiento)
            ->components([

                TextEntry::make('fecha')
                    ->label(__('Último reconocimiento'))
                    // ->hiddenLabel(true)
                    ->maxWidth(Width::Full)
                    ->getStateUsing(function ($record) {
                        if ($record) {
                            return Carbon::parse($record->fecha)->locale(App::getLocale())->translatedFormat('d M Y');
                        }
                        return __('No hay registros');
                    })
                    ->size(TextSize::Large)
                    ->color('indigo')
                    ->badge()
                    ->icon(Heroicon::Calendar)
                    ->fontFamily(FontFamily::Mono),
            ]);
    }

    public function computo(Schema $schema): Schema
    {

        return $schema
            ->record(Computo::where('user_id', Auth::id())->where('year', now()->year)->first())
            ->components([

                TextEntry::make('disponible')
                    ->label(__('Computo'))

                    ->tooltip(__('Horas totales disponibles año actual'))
                    // ->hiddenLabel(true)
                    ->maxWidth(Width::Full)
                    ->size(TextSize::Large)
                    ->color(function ($record) {
                        $min_disponibles = $record->disponible;
                        $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');
                        $restantes = $min_disponibles - $min_solicitados;
                        if ($restantes < 0) {
                            return 'danger';
                        } elseif ($restantes > 0) {
                            return 'success';
                        } else {
                            return 'warning';
                        }
                    })
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        $min_disponibles  = Computo::where('user_id', Auth::id())->where('year', now()->year)->sum('disponible');
                        $min_solicitados = Computo::where('user_id', Auth::id())->where('year', now()->year)
                            ->withSum(['disfrutes as minutos_solicitados_sum' => function ($q) {
                                $q->where('minutos_solicitados', '>', 0);
                            }], 'minutos_solicitados')
                            ->get()
                            ->sum('minutos_solicitados_sum');

                        $restantes = $min_disponibles - $min_solicitados;

                        $restantes_formateados = sprintf('%02d:%02d', intdiv(abs($restantes), 60), abs($restantes) % 60);
                        if ($restantes < 0) {
                            $restantes_formateados = '-' . $restantes_formateados;
                        }

                        return $restantes_formateados;
                    })

                    ->icon(Heroicon::Clock)
                    ->fontFamily(FontFamily::Mono),
            ]);
    }

    public function diasDisponibles(Schema $schema): Schema
    {

        return $schema



            ->components([

                TextEntry::make('disponibles')
                    ->label(__('Dias disponibles'))
                    ->tooltip(__('Días disponibles para solicitar'))
                    // ->inlineLabel()
                    // ->hiddenLabel(true)
                    ->maxWidth(Width::Full)
                    ->size(TextSize::Large)
                    ->color(StatusSolicitudes::Disponible->getColor())
                    ->badge()
                    ->getStateUsing(function () {
                        //obtener dias adicionales
                        $dias_adicionales_totales = Additionalday::where('user_id', Auth::id())
                            ->where('year', now()->year)->count();

                        //contar los días adicionales  se han solicitado disfrutar
                        $dias_adicionales_disfrutados = Disfrute::where('user_id', Auth::id())
                            ->where('disfrutable_type', Additionalday::class)
                            ->where('status', StatusSolicitudes::Aprobado)->count();

                        $dias_adicionales_disponibles = $dias_adicionales_totales - $dias_adicionales_disfrutados;
                        $diasadicionales =  $dias_adicionales_disponibles;

                        //obtener días pedidos por la empresa

                        $dias_totales = Companyday::where('user_id', Auth::id())->count();



                        //contar los días adicionales  se han solicitado disfrutar
                        $dias_disfrutados = Disfrute::where('user_id', Auth::id())->where('disfrutable_type', Companyday::class)
                            ->where('status', StatusSolicitudes::Aprobado)->count();

                        $dias_disponibles = $dias_totales - $dias_disfrutados;
                        $diaspedidosempresa = $dias_disponibles;


                        //obtener sabados
                        //obtener los dias adicionales del usuario para el año actual
                        $sabados_totales = Sabado::where('user_id', Auth::id())
                            ->count();

                        //contar los días adicionales  se han solicitado disfrutar
                        $sabados_disfrutados = Disfrute::where('disfrutable_type', Sabado::class)
                            ->where('user_id', Auth::id())
                            ->where('status', StatusSolicitudes::Aprobado)->count();

                        $sabados_disponibles = $sabados_totales - $sabados_disfrutados;
                        $sabados = $sabados_disponibles;

                        return $diasadicionales + $diaspedidosempresa + $sabados;

                        // return Disfrute::where('user_id', Auth::id())->where('fecha_disfrute', '>=', now())->where('status', 'solicitado')->count();
                    })
                    // ->formatStateUsing(function ($state) {
                    //     return Disfrute::where('user_id', Auth::id())->where('fecha_disfrute', '>', now())->count();
                    // })

                    ->icon('fas-calendar-day')
                    ->fontFamily(FontFamily::Mono),


            ]);
    }

    public function dias(Schema $schema): Schema
    {

        return $schema

            ->model(Disfrute::class)

            ->components([
                // Section::make()
                //   ->maxWidth(Width::Small)
                //     ->label(__('Días de disfrute solicitados y aprobados'))
                // ->hiddenLabel(true)
                // ->inlineLabel()
                //->description(__('Días de disfrute solicitados y aprobados'))
                // ->components([
                TextEntry::make('solicitados')
                    ->label(__('Dias solicitados'))
                    ->tooltip(__('Días solicitados pendientes de aprobar o rechazar'))
                    // ->inlineLabel()
                    // ->hiddenLabel(true)
                    ->maxWidth(Width::Full)
                    ->size(TextSize::Large)
                    ->color(StatusSolicitudes::Solicitado->getColor())
                    ->badge()
                    ->getStateUsing(function () {

                        return Disfrute::where('user_id', Auth::id())->where('fecha_disfrute', '>=', now())->where('status', 'solicitado')->count();
                    })
                    // ->formatStateUsing(function ($state) {
                    //     return Disfrute::where('user_id', Auth::id())->where('fecha_disfrute', '>', now())->count();
                    // })

                    ->icon('fas-calendar-day')
                    ->fontFamily(FontFamily::Mono),

                TextEntry::make('aprobados')
                    ->label(__('Días pendientes de disfrutar'))
                    ->tooltip(__('Días  solicitados y aprobados pendientes de disfrutar'))
                    //  ->inlineLabel()
                    ->maxWidth(Width::Full)
                    ->size(TextSize::Large)
                    ->color('yellow')
                    ->badge()
                    ->getStateUsing(function () {

                        return Disfrute::where('user_id', Auth::id())->where('fecha_disfrute', '>=', now())->where('status', 'aprobado')->count();
                    })


                    ->icon('fas-calendar-day')
                    ->fontFamily(FontFamily::Mono),
                // ])->columns(2)
            ])->columns(2);
    }
}
