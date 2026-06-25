<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_hours',
        'requires_renewal',
        'renewal_years',
    ];

    protected $casts = [
        'requires_renewal' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    // Acciones formativas impartidas de este curso
    public function trainingActions()
    {
        return $this->hasMany(TrainingAction::class);
    }

    // Asistentes a través de las acciones formativas
    public function attendees()
    {
        return User::distinct()
            ->join('training_action_user', 'training_action_user.user_id', '=', 'users.id')
            ->join('training_actions', 'training_actions.id', '=', 'training_action_user.training_action_id')
            ->where('training_actions.course_id', $this->id)
            ->select('users.*')
            ->addSelect(

                'training_action_user.attended as pivot_attended',
                'training_action_user.certificate_path as pivot_certificate_path'
            );
    }

    // Roles asociados al curso
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'course_role');
    }


    /*
    |--------------------------------------------------------------------------
    | Helpers útiles
    |--------------------------------------------------------------------------
    */

    // Saber si el curso tiene renovación
    public function getHasRenewalAttribute()
    {
        return $this->requires_renewal && $this->renewal_years;
    }

    // Texto amigable para mostrar en Filament
    public function getRenewalTextAttribute()
    {
        if (!$this->requires_renewal) {
            return __('No requiere renovación');
        }

        return __('Renovar cada')  . ' ' . $this->renewal_years . ' ' .  __('años');
    }



}
