<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoriaelemento extends Model
{
    protected $table ='categoriaelementos';

    protected $fillable = [
        'nombre_es',
        'nombre_eu',
        'sort',

         'active',
    ];


    protected $casts = [
        'active' => 'boolean'
    ];


    public function elementos()
{
    return $this->hasMany(Elementoinspeccion::class);
}

}
