<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultadoinspeccion extends Model
{
    protected $table = 'resultadoinspecciones';

    protected $fillable = [
        'inspeccion_id',
        'elementoinspeccion_id',
        'resultado',
        'observacion'

    ];

    protected $casts = [
        'resultado' => 'boolean',
    ];

    public function inspeccion()
    {
        return $this->belongsTo(Inspeccion::class);
    }

    public function elemento()
    {
        return $this->belongsTo(Elementoinspeccion::class, 'elementoinspeccion_id');
    }

    public function categoria()
    {
        return $this->hasOneThrough(
            Categoriaelemento::class,
            Elementoinspeccion::class,
            'id', // Foreign key on elementos_inspecciones
            'id', // Foreign key on categoriaelementos
            'elementoinspeccion_id', // Local key on resultadoinspecciones
            'categoriaelemento_id' // Local key on elementos_inspecciones
        );
    }
}
