<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companyday extends Model
{
    protected $fillable = [
        'user_id',
        'fecha',
        'razon',
    ];

    protected $casts = [
        'fecha' => 'date',
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
