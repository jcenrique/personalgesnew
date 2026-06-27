<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LdapRecord\Models\Relations\HasMany;

class Estacion extends Model
{
    protected $table='estaciones';

    protected $fillable = [
        'name',
        'nemonico',
        'pk',
        'zona_id'
    ];


     // Relación con el curso
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function inspecciones()
    {
        return $this->hasMany(Inspeccion::class);
    }
}
