<?php


namespace App\Models;

use Guava\Calendar\Contracts\Eventable;

use Guava\Calendar\ValueObjects\CalendarEvent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrainingAction extends Model implements Eventable
{

    use HasFactory;

    protected $table = 'training_actions';

    protected $fillable = [
        'course_id',
        'company_name',
        'trainer_name',
        'type',
        'start_date',
        'end_date',
        'mode',
        'location',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
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
            ->title(__('Curso') . ':' . $this->course->name)
            ->styles($styles)
            ->allDay()
            ->start($this->start_date)
            ->end($this->end_date)



        ;
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // Relación con el curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Usuarios asistentes (pivot)
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'training_action_user')
            ->withPivot(['attended',  'certificate_path'])

            ->withTimestamps()
            ->distinct();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers útiles
    |--------------------------------------------------------------------------
    */

    // Duración en días (útil para informes)
    public function getDurationDaysAttribute()
    {
        if (!$this->end_date) {
            return 1;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    // Saber si ya ha finalizado
    public function getIsFinishedAttribute()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    // Saber si está en curso
    public function getIsOngoingAttribute()
    {
        return $this->start_date->isPast() && (!$this->end_date || $this->end_date->isFuture());
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'training_action_user')
            //  ->using(\App\Models\TrainingActionUser::class)
            ->withPivot(['attended', 'certificate_path'])
            ->withTimestamps();
    }
}
