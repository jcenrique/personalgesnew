<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingActionUser extends Pivot
{
    use HasFactory;

    protected $table = 'training_action_user';

    protected $fillable = [
        'training_action_id',
        'user_id',
        'attended',
    ];

    protected $casts = [
        'attended' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function trainingAction()
    {
        return $this->belongsTo(TrainingAction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers útiles
    |--------------------------------------------------------------------------
    */

    // Saber si el curso está caducado para este usuario
    public function getIsExpiredAttribute()
    {
        dd('hola');
        if (! $this->trainingAction->end_date) {
            return false;
        }

        $course = $this->trainingAction->course;

        if (! $course->requires_renewal) {
            return false;
        }

        return $this->trainingAction->end_date->addYears($course->renewal_years)->isPast();
    }

    // Fecha de caducidad
    public function getExpiresAtAttribute()
    {
        dd('hola1');
        if (! $this->trainingAction->end_date) {
            return null;
        }

        $course = $this->trainingAction->course;

        if (! $course->requires_renewal) {
            return null;
        }

        return $this->trainingAction->end_date->addYears($course->renewal_years);
    }
}
