<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Elementoinspeccion extends Model
{
    protected $table = 'elementos_inspecciones';

    protected $fillable = [
        'categoriaelemento_id',
        'nombre_es',
        'nombre_eu',
        'active',

    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoriaelemento::class, 'categoriaelemento_id');
    }

    public function resultados()
    {
        return $this->hasMany(Resultadoinspeccion::class, 'elementoinspeccion_id');
    }
}
