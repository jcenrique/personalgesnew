<?php

namespace App\Models;

use App\Enum\StatusSolicitudes;
use App\Observers\DisfruteObserver;
use Carbon\Carbon;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([DisfruteObserver::class])]
class Disfrute extends Model implements Auditable, Eventable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'user_id',
        'fecha_disfrute',
        'minutos_disfrute',
        'status',
        'minutos_solicitados',
    ];

    // This is where you map your model into a calendar object
    public function toCalendarEvent(): CalendarEvent
    {

        // establecer el titulo en funcion del tipo de disfrute, por ejemplo, si es un disfrute de sabados, el titulo sera Sabado, si es Dia adicional, el titulo sera Dia adicional, etc. Para esto, se puede usar el metodo disfrutable para obtener el modelo relacionado y determinar su tipo
        $tipo_disfrute = $this->disfrutable_type;
        // obtener el estado del disfrute a traves de la relacion con el modelo relacionado, por ejemplo, si el disfrute esta relacionado con un modelo de sabados, obtener el estado del sabado a traves de la relacion con el modelo de sabados. Para esto, se puede usar el metodo disfrutable para obtener el modelo relacionado y luego acceder a su estado a traves de la relacion con el modelo de sabados
        $estado_disfrute = $this->status ?? null;

        $styles = [];
        $color = 'orange';

        if ($tipo_disfrute === 'App\Models\Sabado') {
            //  Carbon::parse( $this->disfrutable->sabado_trabajado)->toDayDateTimeString();
            $title = __('Sábado') . ': ' . Carbon::parse($this->disfrutable->sabado_trabajado)->locale(App::getLocale())->translatedFormat('d M Y');
            // establecer el color del evento en funcion del estado del disfrute, por ejemplo, si el disfrute esta aprobado, el color sera verde, si esta pendiente, el color sera amarillo, si esta rechazado, el color sera rojo. Para esto, se puede usar el estado del disfrute obtenido anteriormente para determinar el color del evento
            if ($estado_disfrute === StatusSolicitudes::Aprobado) {
                $color = StatusSolicitudes::Aprobado->getColor();
                $styles = [

                    'color' => 'black !important',
                    // Always applies this font size

                ];
            } elseif ($estado_disfrute === StatusSolicitudes::Solicitado) {

                $title = __('Sábado') . ' ' . __('pendiente aprobación');
                $styles = [

                    'color' => 'black !important',
                    // 'opacity' => 0.8,               // Applies the style if the condition (true) is met
                    'background-color' => StatusSolicitudes::Solicitado->getColor(), // Directly applies the background color
                    'font-size: 14px',             // Always applies this font size

                ];
            }
        } elseif ($tipo_disfrute === 'App\Models\Additionalday') {
            $title = __('Día adicional') . ': ' . $this->disfrutable->year;
            if ($estado_disfrute === StatusSolicitudes::Aprobado) {
                $color = StatusSolicitudes::Aprobado->getColor();
                $styles = [

                    'color' => 'black !important',
                    // Always applies this font size

                ];
            } elseif ($estado_disfrute === StatusSolicitudes::Solicitado) {

                $title = __('Día adicional') . ' ' . __('pendiente aprobación');
                $styles = [

                    'color' => 'black !important',
                    // 'opacity' => 0.5,               // Applies the style if the condition (true) is met
                    'background-color' => StatusSolicitudes::Solicitado->getColor(), // Directly applies the background color
                    'font-size: 14px',             // Always applies this font size

                ];
            }
        } elseif ($tipo_disfrute === 'App\Models\Computo') {
            $title = __('Computo') . ': ' . $this->disfrutable->year;
            if ($estado_disfrute === StatusSolicitudes::Aprobado) {
                $color = StatusSolicitudes::Aprobado->getColor();
                $styles = [

                    'color' => 'black !important',
                    // Always applies this font size

                ];
            } elseif ($estado_disfrute === StatusSolicitudes::Solicitado) {
                $title = __('Computo') . ' ' . __('pendiente aprobación');
                $styles = [

                    'color' => 'black !important',
                    // Applies the style if the condition (true) is met
                    'background-color' => StatusSolicitudes::Solicitado->getColor(), // Directly applies the background color
                    'font-size: 14px',             // Always applies this font size

                ];
            }
        } elseif ($tipo_disfrute === 'App\Models\Companyday') {
            $title = __('Día pedido Empresa') .  ': ' . Carbon::parse($this->disfrutable->fecha)->locale(App::getLocale())->translatedFormat('d M Y');
            if ($estado_disfrute === StatusSolicitudes::Aprobado) {
                $color = StatusSolicitudes::Aprobado->getColor();
                $styles = [

                    'color' => 'black !important',
                    // Always applies this font size

                ];
            } elseif ($estado_disfrute === StatusSolicitudes::Solicitado) {
                $title = __('Día pedido Empresa') . ' ' . __('pendiente aprobación');
                $styles = [

                    'color' => 'black !important',
                    // Applies the style if the condition (true) is met
                    'background-color' => StatusSolicitudes::Solicitado->getColor(), // Directly applies the background color
                    'font-size: 14px',             // Always applies this font size

                ];
            }
        } else {
            $title = __('Disfrute');
            if ($estado_disfrute === StatusSolicitudes::Aprobado) {
                $color = StatusSolicitudes::Aprobado->getColor();
                $styles = [

                    'color' => 'black !important',
                    // Always applies this font size

                ];
            } elseif ($estado_disfrute === StatusSolicitudes::Solicitado) {
                $styles = [
                    'color' => 'black !important',
                    // Applies the style if the condition (true) is met
                    'background-color' => StatusSolicitudes::Solicitado->getColor(), // Directly applies the background color
                    'font-size: 14px',             // Always applies this font size

                ];
            }
        }

        // For eloquent models, make sure to pass the model to the constructor
        return CalendarEvent::make($this)
            // crear accion para ir al recurso relacionado con el disfrute
            ->allDay(false)
            ->title($title)
            ->start($this->fecha_disfrute->copy()->startOfDay())
            ->end($this->fecha_disfrute->copy()->endOfDay())
            ->styles($styles)
            ->backgroundColor($color);
    }

    protected function casts(): array
    {
        return [

            'fecha_disfrute' => 'date',
            'status' => StatusSolicitudes::class,
        ];
    }

    public function disfrutable()
    {
        return $this->morphTo();
    }

    public function sabados()
    {
        return $this->hasMany(Sabado::class, 'disfrutable_id')->where('disfrutable_type', Sabado::class);
    }

    public function additionaldays()
    {
        return $this->hasMany(Additionalday::class, 'disfrutable_id')->where('disfrutable_type', Additionalday::class);
    }

    public function companydays()
    {
        return $this->hasMany(Companyday::class, 'disfrutable_id')->where('disfrutable_type', Companyday::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function computo()
    {
        return $this->belongsTo(Computo::class, 'disfrutable_id')->where('disfrutable_type', Computo::class);
    }
}
