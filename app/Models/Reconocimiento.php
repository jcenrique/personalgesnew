<?php

namespace App\Models;

use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Model;

class Reconocimiento extends Model implements Eventable
{
    protected $fillable = [
        'lugar',
        'user_id',
        'fecha',
        'años',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // scope para devolver solo los ultimos reconocimientos

    public function scopeLatestPerUser($query)
    {
        return $query->whereIn('id', function ($sub) {
            $sub->selectRaw('id')
                ->from('reconocimientos as r1')
                ->whereRaw('fecha = (
                SELECT MAX(fecha)
                FROM reconocimientos r2
                WHERE r2.user_id = r1.user_id
            )');
        });
    }

    // This is where you map your model into a calendar resource object
    public function toCalendarEvent(): CalendarEvent
    {
        $styles = [
            'color' => '#14532d !important',
            // Applies the style if the condition (true) is met
            'background-color' => '#C7D2FE !important', // Directly applies the background color
            'font-size: 14px',             // Always applies this font size

        ];

        return CalendarEvent::make($this)
            ->title(__('Reconocimiento médico'))
            ->styles($styles)
            ->allDay(false)
            ->start($this->fecha->copy()->startOfDay())
            ->end($this->fecha->copy()->endOfDay());
    }
}
