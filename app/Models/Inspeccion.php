<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Inspeccion extends Model
{
    protected $table = 'inspecciones';

    protected $fillable = [
        'user_id_1', //usuario Jeje de servicio
        'user_id_2', //usuario TR o TRH

        'estacion_id',
        'fecha_hora',
        'type',
        'observaciones',

        // nuevos campos
        'tema',
        'cuestiones',
        'fecha_comunicacion',
        'actions',

    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'fecha_comunicacion' => 'date',
        'actions' => 'boolean' ,
    ];

    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id_1');
    }


    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id_2');
    }

    public function estacion()
    {
        return $this->belongsTo(Estacion::class);
    }

    public function resultados()
    {
        return $this->hasMany(Resultadoinspeccion::class);
    }

     public function getNombreEstacionAttribute()
    {
        //devuelve el nombre de la estación en mayusculas
        return strtoupper($this->estacion->name);

    }
}
