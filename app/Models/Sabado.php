<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Sabado extends Model implements Auditable
{
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'sabado_trabajado',
        'user_id',
        'status',

    ];

    // borrar en cascada el disfrute relacionado al eliminar un sábado
    protected static function booted()
    {
        static::deleting(function ($sabado) {
            $sabado->disfrute()->delete();
        });
    }

    protected function casts(): array
    {
        return [
            'sabado_trabajado' => 'date',

        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function disfrute()
    {
        return $this->morphOne(Disfrute::class, 'disfrutable');
    }

    public function rechazos()
    {
        return $this->morphMany(Rechazo::class, 'rechazable');
    }
}
