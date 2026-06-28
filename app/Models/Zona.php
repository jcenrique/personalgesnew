<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Zona extends Model
{
    protected $fillable = ['name'];

    public function residencias()
    {
        return $this->hasMany(Residencia::class);
    }

    public function estaciones()
    {
        return $this->hasMany(Estacion::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
