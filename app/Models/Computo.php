<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Computo extends Model implements Auditable
{
     use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'year',
        'user_id',
        'disponible'


    ];

    // borrar en cascada el disfrute relacionado al eliminar un sábado
    protected static function booted()
    {
        static::deleting(function ($computo) {
            $computo->disfrutes()->delete();
        });
    }

    protected function casts(): array
    {
        return [

            'year' => 'integer',


        ];
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function disfrutes()
    {
        return $this->morphMany(Disfrute::class, 'disfrutable');
    }


    public function rechazos()
    {
        return $this->morphMany(Rechazo::class, 'rechazable');
    }
}
