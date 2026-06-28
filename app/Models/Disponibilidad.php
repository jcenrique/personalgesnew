<?php

namespace App\Models;

use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Disponibilidad extends Model implements Auditable, Eventable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'disponibilidades';

    protected $fillable = [
        'user_id',
        'fecha',
        'year',
        'razon',

    ];

    protected $casts = [
        'fecha' => 'date',

    ];

    // This is where you map your model into a calendar resource object
    public function toCalendarEvent(): CalendarEvent
    {
        $styles = [
            'color' => '#14532d !important',
            // Applies the style if the condition (true) is met
            'background-color' => '#bbf7d0 !important', // Directly applies the background color
            'font-size: 14px',             // Always applies this font size

        ];

        return CalendarEvent::make($this)
            ->title(__('Disponibilidad'))
            ->styles($styles)
            ->allDay()
            ->start($this->fecha)
            ->end($this->fecha);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
