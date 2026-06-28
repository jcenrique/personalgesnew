<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class Additionalday extends Model implements Auditable
{
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'year',
        'user_id',
        'status',

    ];

    // borrar en cascada el disfrute relacionado al eliminar un sábado
    protected static function booted()
    {
        static::deleting(function ($additionalday) {
            $additionalday->disfrute()->delete();
        });
    }

    protected $casts = [
        'year' => 'integer',
    ];

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
